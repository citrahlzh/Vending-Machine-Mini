<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuditLogService
{
    private static ?bool $auditTableExists = null;

    public function logRequest(Request $request, SymfonyResponse $response): void
    {
        if (!$this->shouldLogRequest($request)) {
            return;
        }

        $input = $request->isMethod('GET')
            ? $request->query()
            : $request->except($this->sensitiveKeys());

        $this->create([
            'channel' => 'request',
            'event' => 'request.completed',
            'action' => strtolower($request->method()),
            'description' => $this->buildRequestDescription($request, $response->getStatusCode()),
            'subject_label' => $request->route()?->getName() ?: $request->path(),
            'route_name' => $request->route()?->getName(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $response->getStatusCode(),
            'tags' => $this->filterEmpty([
                'guarded' => $request->user() ? 'authenticated' : 'guest',
                'area' => $request->is('api/*') ? 'api' : 'web',
            ]),
            'properties' => $this->filterEmpty([
                'request' => [
                    'path' => $request->path(),
                    'query' => $request->query(),
                    'input' => $this->sanitizeData($input),
                ],
            ]),
        ], $request->user());
    }

    public function logAuthEvent(
        string $event,
        ?Model $actor,
        Request $request,
        array $properties = [],
        ?int $statusCode = null
    ): void {
        $this->create([
            'channel' => 'auth',
            'event' => $event,
            'action' => str_contains($event, 'logout') ? 'logout' : 'login',
            'description' => $properties['description'] ?? $this->defaultAuthDescription($event, $actor),
            'subject_label' => $actor ? $this->resolveModelLabel($actor) : ($request->input('username') ?: 'guest'),
            'route_name' => $request->route()?->getName(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $statusCode,
            'tags' => ['auth'],
            'properties' => $this->sanitizeData($properties),
        ], $actor);
    }

    public function logModelEvent(string $event, Model $subject, array $changes = [], ?Model $actor = null): void
    {
        if ($subject instanceof AuditLog) {
            return;
        }

        $this->create([
            'channel' => 'model',
            'event' => $event,
            'action' => $this->extractActionFromEvent($event),
            'description' => $this->buildModelDescription($event, $subject),
            'subject_label' => $this->resolveModelLabel($subject),
            'tags' => ['model', class_basename($subject)],
            'properties' => $this->sanitizeData([
                'changes' => $changes,
            ]),
        ], $actor, $subject);
    }

    public function logBusinessEvent(
        string $event,
        string $description,
        array $properties = [],
        ?Model $actor = null,
        ?Model $subject = null
    ): void {
        $this->create([
            'channel' => 'business',
            'event' => $event,
            'action' => $this->extractActionFromEvent($event),
            'description' => $description,
            'subject_label' => $subject ? $this->resolveModelLabel($subject) : null,
            'tags' => ['business'],
            'properties' => $this->sanitizeData($properties),
        ], $actor, $subject);
    }

    private function create(array $attributes, ?Model $actor = null, ?Model $subject = null): void
    {
        if (!$this->auditTableExists()) {
            return;
        }

        $payload = $attributes;

        if ($actor) {
            $payload['actor_type'] = $actor::class;
            $payload['actor_id'] = $actor->getKey();
            $payload['actor_name'] = $this->resolveModelLabel($actor);
        }

        if ($subject) {
            $payload['subject_type'] = $subject::class;
            $payload['subject_id'] = $subject->getKey();
            $payload['subject_label'] = $payload['subject_label'] ?? $this->resolveModelLabel($subject);
        }

        AuditLog::query()->create($payload);
    }

    private function auditTableExists(): bool
    {
        if (self::$auditTableExists !== null) {
            return self::$auditTableExists;
        }

        return self::$auditTableExists = Schema::hasTable('audit_logs');
    }

    private function shouldLogRequest(Request $request): bool
    {
        if ($request->routeIs('image')) {
            return false;
        }

        if ($request->is('up')) {
            return false;
        }

        return $request->route() !== null;
    }

    private function buildRequestDescription(Request $request, int $statusCode): string
    {
        $routeName = $request->route()?->getName();
        $target = $routeName ?: $request->path();

        return sprintf(
            'Request %s %s selesai dengan status %s.',
            strtoupper($request->method()),
            $target,
            $statusCode
        );
    }

    private function defaultAuthDescription(string $event, ?Model $actor): string
    {
        return match ($event) {
            'auth.login.success' => sprintf('%s berhasil login.', $actor ? $this->resolveModelLabel($actor) : 'User'),
            'auth.login.failed' => 'Percobaan login gagal.',
            'auth.logout' => sprintf('%s logout.', $actor ? $this->resolveModelLabel($actor) : 'User'),
            default => 'Aktivitas autentikasi tercatat.',
        };
    }

    private function buildModelDescription(string $event, Model $subject): string
    {
        $label = $this->resolveModelLabel($subject);
        $modelName = class_basename($subject);

        return match ($event) {
            'model.created' => "{$modelName} {$label} dibuat.",
            'model.updated' => "{$modelName} {$label} diperbarui.",
            'model.deleted' => "{$modelName} {$label} dihapus.",
            'model.restored' => "{$modelName} {$label} dipulihkan.",
            default => "{$modelName} {$label} berubah.",
        };
    }

    private function extractActionFromEvent(string $event): string
    {
        return strtolower((string) str($event)->afterLast('.'));
    }

    private function resolveModelLabel(Model $model): string
    {
        foreach (['name', 'title', 'product_name', 'label', 'username', 'idempotency_key', 'code'] as $attribute) {
            $value = data_get($model, $attribute);
            if (filled($value)) {
                return (string) $value;
            }
        }

        return class_basename($model) . '#' . $model->getKey();
    }

    private function sanitizeData(array $data): array
    {
        foreach ($this->sensitiveKeys() as $key) {
            if (Arr::has($data, $key)) {
                Arr::set($data, $key, '[REDACTED]');
            }
        }

        return $this->replaceUploadedFiles($data);
    }

    private function replaceUploadedFiles(array $data): array
    {
        array_walk_recursive($data, function (&$value): void {
            if (is_object($value) && method_exists($value, 'getClientOriginalName')) {
                $value = $value->getClientOriginalName();
            }
        });

        return $data;
    }

    private function filterEmpty(array $data): array
    {
        return array_filter($data, function ($value) {
            return !is_null($value) && $value !== [] && $value !== '';
        });
    }

    private function sensitiveKeys(): array
    {
        return [
            'password',
            'password_confirmation',
            'remember_token',
            'token',
            'access_token',
            'refresh_token',
            'signature_key',
        ];
    }
}
