<?php

namespace App\Console\Commands;

use App\Services\TMS\HeartbeatService;
use Illuminate\Console\Command;

class TmsHeartbeat extends Command
{
    protected $signature   = 'tms:heartbeat';
    protected $description = 'Kirim heartbeat ke TMS dan ambil perintah antrian';

    public function handle(HeartbeatService $service): void
    {
        $this->info('[TMS] Mengirim heartbeat...');
        $service->send();
        $this->info('[TMS] Selesai.');
    }
}
