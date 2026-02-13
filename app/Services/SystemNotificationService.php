<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SystemNotification;

class SystemNotificationService
{
    public function notifyActiveUsers(
        string $title,
        string $message,
        string $type = 'info',
        ?string $actionUrl = null,
        array $meta = []
    ): void {
        User::query()
            ->where('is_active', true)
            ->get()
            ->each(function (User $user) use ($title, $message, $type, $actionUrl, $meta) {
                $user->notify(new SystemNotification(
                    title: $title,
                    message: $message,
                    type: $type,
                    actionUrl: $actionUrl,
                    meta: $meta
                ));
            });
    }
}

