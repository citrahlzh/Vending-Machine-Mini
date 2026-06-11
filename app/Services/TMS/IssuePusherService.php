<?php

namespace App\Services\TMS;

use App\Models\TmsPushQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IssuePusherService
{
    public function push(int $vmSaleId, string $issueType, array $extra = []): void
    {
        $payload = array_merge([
            'machine_code'   => config('tms.machine_code'),
            'vm_sale_id'     => $vmSaleId,
            'transaction_at' => now()->toIso8601String(),
            'issue_type'     => $issueType,
            // issue_type: dispense_failed | dispense_partial | payment_mismatch
        ], $extra);

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-Machine-Key' => config('tms.api_key')])
                ->post(config('tms.base_url') . '/api/vm/issues/push', $payload);

            if ($response->successful()) {
                Log::info("[TMS] Issue push berhasil. vm_sale_id={$vmSaleId}, type={$issueType}");
            } else {
                $this->queueForRetry($payload);
                Log::warning("[TMS] Issue push gagal, masuk antrian retry. vm_sale_id={$vmSaleId}");
            }
        } catch (\Throwable $e) {
            $this->queueForRetry($payload);
            Log::error("[TMS] Issue push exception: " . $e->getMessage());
        }
    }

    private function queueForRetry(array $payload): void
    {
        TmsPushQueue::firstOrCreate(
            ['type' => 'issue', 'payload->vm_sale_id' => $payload['vm_sale_id']],
            ['type' => 'issue', 'payload' => $payload, 'retry_count' => 0]
        );
    }
}
