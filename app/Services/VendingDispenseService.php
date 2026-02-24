<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VendingDispenseService
{
    public function dispense(string $transactionId, string $cellCode): bool
    {
        $endpoint = (string) env('VENDING_DISPENSE_URL', 'http://127.0.0.1:9000/dispense');

        try {
            $response = Http::timeout(8)->post($endpoint, [
                'transaction_id' => $transactionId,
                'cell_id' => $cellCode,
            ]);
        } catch (\Throwable $e) {
            Log::error('Dispense API request failed', [
                'transaction_id' => $transactionId,
                'cell_id' => $cellCode,
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        if (!$response->successful()) {
            Log::warning('Dispense API returned non-success status', [
                'transaction_id' => $transactionId,
                'cell_id' => $cellCode,
                'http_status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        $payload = $response->json();
        $ok = (bool) data_get($payload, 'ok', false);

        if (!$ok) {
            Log::warning('Dispense API returned failed payload', [
                'transaction_id' => $transactionId,
                'cell_id' => $cellCode,
                'payload' => $payload,
            ]);
        }

        return $ok;
    }
}
