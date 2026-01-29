<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleLine;

class TransactionService{
    public function createTransaction(Request $request): Sale {
        $saleData = $request->only([
            'idempotency_key',
            'qris_id',
            'transaction_date',
            'status',
            'dispense_status',
            'total_amount'
        ]);

        $sale = Sale::create($saleData);

        $saleLinesData = $request->input('salesLines', []);
        foreach ($saleLinesData as $lineData) {
            $lineData['sale_id'] = $sale->id;
            SaleLine::create($lineData);
        }

        return $sale->load('salesLines');
    }
}
