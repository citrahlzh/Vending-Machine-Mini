<?php

namespace App\Services\TMS;

use App\Models\TmsPushQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushQueueService
{
    public function retryPending(): void
    {
        $pending = TmsPushQueue::pending()->get();

        foreach ($pending as $item) {
            $item->update(['last_tried_at' => now()]);

            try {
                $endpoint = match ($item->type) {
                    'transaction' => '/api/vm/transactions/push',
                    'issue'       => '/api/vm/issues/push',
                    default       => null,
                };

                if (!$endpoint) continue;

                $response = Http::timeout(15)
                    ->withHeaders(['X-Machine-Key' => config('tms.api_key')])
                    ->post(config('tms.base_url') . $endpoint, $item->payload);

                if ($response->successful()) {
                    $item->update(['pushed_at' => now()]);
                    Log::info("[TMS] Retry push queue ID {$item->id} berhasil.");
                } else {
                    $item->increment('retry_count');
                    Log::warning("[TMS] Retry push queue ID {$item->id} gagal lagi. Retry ke-{$item->retry_count}.");
                }
            } catch (\Throwable $e) {
                $item->increment('retry_count');
                Log::error("[TMS] Retry push queue exception ID {$item->id}: " . $e->getMessage());
            }
        }
    }
}
