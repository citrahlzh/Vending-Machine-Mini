<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VendingDispenseService
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function dispense(string $transactionId, string $cellCode, array $context = []): array
    {
        $endpoint = (string) env('VENDING_DISPENSE_URL', 'http://127.0.0.1:9000/dispense');
        $timeout = (float) env('VENDING_DISPENSE_TIMEOUT_SECONDS', 0.25);
        $timeout = $timeout > 0 ? $timeout : 0.25;

        $baseContext = array_merge($context, [
            'transaction_id' => $transactionId,
            'cell_id' => $cellCode,
            'endpoint' => $endpoint,
        ]);

        $this->auditLogService->logBusinessEvent(
            'dispense.requested',
            "Permintaan dispense dikirim untuk cell {$cellCode}.",
            $baseContext
        );

        try {
            $response = Http::connectTimeout($timeout)->timeout($timeout)->post($endpoint, [
                'transaction_id' => $transactionId,
                'cell_id' => $cellCode,
            ]);

            $payload = array_merge($baseContext, [
                'http_status' => $response->status(),
                'response_body' => $response->json() ?? $response->body(),
                'successful' => $response->successful(),
            ]);

            $this->auditLogService->logBusinessEvent(
                $response->successful() ? 'dispense.succeeded' : 'dispense.failed',
                $response->successful()
                    ? "Dispense untuk cell {$cellCode} berhasil dipanggil."
                    : "Dispense untuk cell {$cellCode} merespons gagal.",
                $payload
            );

            return [
                'ok' => $response->successful(),
                'status_code' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::warning('Dispense API request failed (ignored)', [
                'transaction_id' => $transactionId,
                'cell_id' => $cellCode,
                'error' => $e->getMessage(),
            ]);

            $this->auditLogService->logBusinessEvent(
                'dispense.failed',
                "Dispense untuk cell {$cellCode} gagal dipanggil.",
                array_merge($baseContext, [
                    'error' => $e->getMessage(),
                ])
            );

            return [
                'ok' => false,
                'status_code' => null,
                'body' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
