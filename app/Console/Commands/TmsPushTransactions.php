<?php

namespace App\Console\Commands;

use App\Services\TMS\TransactionPusherService;
use Illuminate\Console\Command;

class TmsPushTransactions extends Command
{
    protected $signature   = 'tms:push-transactions {date? : Tanggal format Y-m-d, default kemarin}';
    protected $description = 'Push summary transaksi harian ke TMS';

    public function handle(TransactionPusherService $service): void
    {
        $date = $this->argument('date') ?? now()->subDay()->toDateString();
        $this->info("[TMS] Pushing transaksi untuk tanggal: {$date}");
        $service->pushForDate($date);
        $this->info('[TMS] Selesai.');
    }
}
