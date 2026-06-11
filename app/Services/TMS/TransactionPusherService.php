<?php

namespace App\Services\TMS;

use App\Models\TmsPushQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionPusherService
{
    public function pushDaily(): void
    {
        // Ambil summary kemarin (bisa disesuaikan ke range yang diperlukan)
        $date = now()->subDay()->toDateString();
        $this->pushForDate($date);
    }

    public function pushForDate(string $date): void
    {
        try {
            // Query summary dari tabel sales VM
            // Sesuaikan query ini dengan struktur tabel sales di VM kamu
            $sales = DB::table('sales')
                ->where('machine_id', config('tms.machine_code'))
                ->whereDate('created_at', $date)
                ->selectRaw('
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful_transactions,
                    SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_transactions,
                    SUM(CASE WHEN status = "partial" THEN 1 ELSE 0 END) as partial_transactions,
                    SUM(CASE WHEN status = "success" THEN total_amount ELSE 0 END) as total_revenue
                ')
                ->first();

            // Query breakdown produk
            $products = DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->where('sales.machine_id', config('tms.machine_code'))
                ->whereDate('sales.created_at', $date)
                ->where('sales.status', 'success')
                ->selectRaw('product_name, sku, SUM(qty) as qty_sold, unit_price')
                ->groupBy('sku', 'product_name', 'unit_price')
                ->get()
                ->toArray();

            $payload = [
                'machine_code'            => config('tms.machine_code'),
                'summary_date'            => $date,
                'total_transactions'      => $sales->total_transactions ?? 0,
                'successful_transactions' => $sales->successful_transactions ?? 0,
                'failed_transactions'     => $sales->failed_transactions ?? 0,
                'partial_transactions'    => $sales->partial_transactions ?? 0,
                'total_revenue'           => $sales->total_revenue ?? 0,
                'products'                => $products,
            ];

            $response = Http::timeout(15)
                ->withHeaders(['X-Machine-Key' => config('tms.api_key')])
                ->post(config('tms.base_url') . '/api/vm/transactions/push', $payload);

            if ($response->successful()) {
                Log::info("[TMS] Push transaksi {$date} berhasil.");
            } else {
                $this->queueForRetry('transaction', $payload);
                Log::warning("[TMS] Push transaksi {$date} gagal, masuk antrian retry.");
            }
        } catch (\Throwable $e) {
            $this->queueForRetry('transaction', $payload ?? []);
            Log::error("[TMS] Push transaksi exception: " . $e->getMessage());
        }
    }

    private function queueForRetry(string $type, array $payload): void
    {
        TmsPushQueue::firstOrCreate(
            ['type' => $type, 'payload->summary_date' => $payload['summary_date'] ?? null],
            ['type' => $type, 'payload' => $payload, 'retry_count' => 0]
        );
    }
}
