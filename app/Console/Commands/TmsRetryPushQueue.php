<?php

namespace App\Console\Commands;

use App\Services\TMS\PushQueueService;
use Illuminate\Console\Command;

class TmsRetryPushQueue extends Command
{
    protected $signature   = 'tms:retry-push';
    protected $description = 'Retry push data yang gagal saat offline';

    public function handle(PushQueueService $service): void
    {
        $service->retryPending();
    }
}
