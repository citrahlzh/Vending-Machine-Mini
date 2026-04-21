<?php

namespace App\Http\Middleware;

use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuditRequestActivity
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
        } catch (Throwable $throwable) {
            $this->auditLogService->logBusinessEvent(
                'request.failed',
                'Request gagal diproses.',
                [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'route_name' => $request->route()?->getName(),
                    'error' => $throwable->getMessage(),
                ],
                $request->user()
            );

            throw $throwable;
        }

        $this->auditLogService->logRequest($request, $response);

        return $response;
    }
}
