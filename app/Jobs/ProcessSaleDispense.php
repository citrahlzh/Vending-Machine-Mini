<?php

namespace App\Jobs;

use App\Services\TransactionService;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessSaleDispense
{
    use Dispatchable;

    public function __construct(
        public int $saleId
    ) {
    }

    public function handle(TransactionService $transactionService): void
    {
        $transactionService->processDispenseForPaidSale($this->saleId);
    }
}
