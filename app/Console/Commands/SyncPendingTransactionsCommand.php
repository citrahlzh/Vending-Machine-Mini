<?php

namespace App\Console\Commands;

use App\Services\MidtransQrisService;
use App\Services\TransactionService;
use Illuminate\Console\Command;

class SyncPendingTransactionsCommand extends Command
{
    protected $signature = 'transactions:sync-pending {--limit=50 : Maximum pending transactions checked per run}';

    protected $description = 'Sync pending transactions with Midtrans without webhook';

    public function handle(TransactionService $transactionService, MidtransQrisService $qrisService): int
    {
        $limit = (int) $this->option('limit');
        $result = $transactionService->syncPendingPayments($qrisService, $limit);

        $this->info(sprintf(
            'Pending sync completed. checked=%d updated=%d failed=%d',
            $result['checked'],
            $result['updated'],
            $result['failed']
        ));

        return self::SUCCESS;
    }
}
