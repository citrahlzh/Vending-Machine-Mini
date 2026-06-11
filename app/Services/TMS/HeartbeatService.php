<?php

namespace App\Services\TMS;

use App\Models\TmsCommandQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HeartbeatService
{
    public function send(): void
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-Machine-Key' => config('tms.api_key')])
                ->post(config('tms.base_url') . '/api/vm/heartbeat', [
                    'machine_code'     => config('tms.machine_code'),
                    'app_version'      => config('app.version', '1.0.0'),
                    'firmware_version' => php_uname('r'),
                    'ip_address'       => $this->getLocalIp(),
                ]);

            if ($response->successful()) {
                $commands = $response->json('commands', []);
                $this->storeCommands($commands);
                Log::info('[TMS] Heartbeat OK. Commands diterima: ' . count($commands));
            } else {
                Log::warning('[TMS] Heartbeat gagal. Status: ' . $response->status());
            }
        } catch (\Throwable $e) {
            // Jangan crash — VM harus tetap jalan meski TMS tidak bisa dihubungi
            Log::error('[TMS] Heartbeat exception: ' . $e->getMessage());
        }
    }

    private function storeCommands(array $commands): void
    {
        foreach ($commands as $cmd) {
            TmsCommandQueue::firstOrCreate(
                ['tms_command_id' => $cmd['id']],
                [
                    'type'        => $cmd['type'],
                    'payload'     => $cmd['payload'] ?? null,
                    'status'      => 'pending',
                    'received_at' => now(),
                ]
            );
        }
    }

    private function getLocalIp(): string
    {
        try {
            return gethostbyname(gethostname());
        } catch (\Throwable $e) {
            return '0.0.0.0';
        }
    }
}
