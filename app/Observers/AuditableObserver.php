<?php

namespace App\Observers;

use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditableObserver
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function created(Model $model): void
    {
        $this->auditLogService->logModelEvent(
            'model.created',
            $model,
            [
                'attributes' => $model->getAttributes(),
            ],
            Auth::user()
        );
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        $oldValues = [];
        foreach (array_keys($changes) as $key) {
            $oldValues[$key] = $model->getOriginal($key);
        }

        $this->auditLogService->logModelEvent(
            'model.updated',
            $model,
            [
                'old' => $oldValues,
                'new' => $changes,
            ],
            Auth::user()
        );
    }

    public function deleted(Model $model): void
    {
        $this->auditLogService->logModelEvent(
            'model.deleted',
            $model,
            [
                'attributes' => $model->getAttributes(),
            ],
            Auth::user()
        );
    }

    public function restored(Model $model): void
    {
        $this->auditLogService->logModelEvent(
            'model.restored',
            $model,
            [
                'attributes' => $model->getAttributes(),
            ],
            Auth::user()
        );
    }
}
