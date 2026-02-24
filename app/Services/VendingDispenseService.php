<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VendingDispenseService
{
    public function dispense(string $transactionId, string $cellCode): void
    {
        $endpoint = (string) env('VENDING_DISPENSE_URL', 'http://127.0.0.1:9000/dispense');

        try {
            Http::timeout(8)->post($endpoint, [
                'transaction_id' => $transactionId,
                'cell_id' => $cellCode,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Dispense API request failed (ignored)', [
                'transaction_id' => $transactionId,
                'cell_id' => $cellCode,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
