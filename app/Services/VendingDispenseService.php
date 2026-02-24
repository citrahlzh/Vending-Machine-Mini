<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VendingDispenseService
{
    public function dispense(string $transactionId, string $cellCode): void
    {
        $endpoint = (string) env('VENDING_DISPENSE_URL', 'http://127.0.0.1:9000/dispense');
        $timeout = (float) env('VENDING_DISPENSE_TIMEOUT_SECONDS', 0.25);
        $timeout = $timeout > 0 ? $timeout : 0.25;

        try {
            Http::connectTimeout($timeout)->timeout($timeout)->post($endpoint, [
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
