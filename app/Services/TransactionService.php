<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\ProductDisplay;
use App\Services\MidtransQrisService;
use Carbon\Carbon;
use Exception;

class TransactionService{
    public function checkout(Request $request, MidtransQrisService $qrisService): array
    {
        $items = collect($request->input('items', []));
        $orderId = $request->input('idempotency_key', (string) Str::uuid());

        $existing = Sale::with('salesLines')->where('idempotency_key', $orderId)->first();
        if ($existing) {
            return [
                'sale' => $existing,
                'payment' => null,
                'is_duplicate' => true,
            ];
        }

        $sale = DB::transaction(function () use ($items, $orderId) {
            $normalizedItems = $items
                ->groupBy('product_display_id')
                ->map(function ($rows) {
                    return $rows->sum(function ($row) {
                        return (int) ($row['qty'] ?? 1);
                    });
                });

            $displayIds = $normalizedItems->keys()->values();

            $productDisplays = ProductDisplay::with(['price', 'cell'])
                ->whereIn('id', $displayIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $totalAmount = 0;

            foreach ($normalizedItems as $displayId => $qty) {
                $display = $productDisplays->get((int) $displayId);
                if (!$display) {
                    throw new Exception('Product display not found.');
                }
                                if ($display->status !== 'active') {
                    throw new Exception('Product display is not active.');
                }
if ($display->is_empty || !$display->cell || (int) $display->cell->qty_current <= 0) {
                    throw new Exception('Product out of stock.');
                }
                if (!$display->price || !$display->price->is_active) {
                    throw new Exception('Product price is not available.');
                }

                $qty = (int) $qty;
                if ($qty < 1) {
                    throw new Exception('Invalid product quantity.');
                }

                if ($qty > (int) $display->cell->qty_current) {
                    throw new Exception('Requested quantity exceeds available stock.');
                }

                $totalAmount += ((int) $display->price->price) * $qty;
            }

            $sale = Sale::create([
                'idempotency_key' => $orderId,
                'qris_id' => $orderId,
                'transaction_date' => Carbon::now(),
                'status' => 'pending',
                'dispense_status' => 'pending',
                'total_amount' => $totalAmount,
            ]);

            foreach ($normalizedItems as $displayId => $qty) {
                $qty = (int) $qty;
                for ($i = 0; $i < $qty; $i++) {
                    SaleLine::create([
                        'sale_id' => $sale->id,
                        'product_display_id' => (int) $displayId,
                        'status' => 'pending',
                    ]);
                }
            }

            return $sale->load('salesLines');
        });

        try {
            $charge = $qrisService->charge($sale->idempotency_key, $sale->total_amount);
        } catch (Exception $e) {
            $sale->update(['status' => 'failed', 'dispense_status' => 'failed']);
            $sale->salesLines()->update(['status' => 'failed']);
            throw $e;
        }

        $sale->update([
            'qris_id' => $charge->transaction_id ?? $sale->idempotency_key,
        ]);

        return [
            'sale' => $sale->refresh()->load('salesLines'),
            'payment' => $qrisService->extractQrisData($charge),
            'is_duplicate' => false,
        ];
    }

    public function handleNotification(array $payload): ?Sale
    {
        $orderId = $payload['order_id'] ?? null;
        if (!$orderId) {
            return null;
        }

        $sale = Sale::with('salesLines')->where('idempotency_key', $orderId)->first();
        if (!$sale) {
            return null;
        }

        $mapped = $this->mapMidtransStatus($payload['transaction_status'] ?? null) ?? $sale->status;

        return $this->applySaleStatus(
            $sale,
            $mapped,
            $payload['transaction_id'] ?? null
        );
    }

    public function syncPaymentStatus(Sale $sale, MidtransQrisService $qrisService): Sale
    {
        $status = $qrisService->status($sale->idempotency_key);

        $mapped = $this->mapMidtransStatus(data_get($status, 'transaction_status')) ?? $sale->status;

        return $this->applySaleStatus(
            $sale,
            $mapped,
            data_get($status, 'transaction_id')
        );
    }

    public function cancelPendingPayment(Sale $sale, MidtransQrisService $qrisService): Sale
    {
        $sale = $sale->fresh(['salesLines']);

        if (!$sale) {
            throw new Exception('Transaction not found.');
        }

        if ($sale->status === 'paid') {
            throw new Exception('Payment already completed and cannot be canceled.');
        }

        if (in_array($sale->status, ['failed', 'expired'], true)) {
            return $sale;
        }

        try {
            $qrisService->expire($sale->idempotency_key);
        } catch (Exception $expireException) {
            try {
                $qrisService->cancel($sale->idempotency_key);
            } catch (Exception $cancelException) {
                throw $expireException;
            }
        }

        return $this->applySaleStatus($sale, 'expired', null);
    }

    private function mapMidtransStatus(?string $status): ?string
    {
        return match ($status) {
            'capture', 'settlement' => 'paid',
            'pending' => 'pending',
            'expire' => 'expired',
            'cancel', 'deny' => 'failed',
            default => null,
        };
    }

    private function applySaleStatus(Sale $sale, string $status, ?string $transactionId): Sale
    {
        $sale->status = $status;
        if (!empty($transactionId)) {
            $sale->qris_id = $transactionId;
        }
        $sale->save();

        if ($status === 'paid') {
            return $this->finalizeDispense($sale);
        }

        if (in_array($status, ['failed', 'expired'], true)) {
            $sale->salesLines()->update(['status' => 'failed']);
            if ($sale->dispense_status === 'pending') {
                $sale->dispense_status = 'failed';
                $sale->save();
            }
        }

        return $sale->refresh()->load('salesLines');
    }

    private function finalizeDispense(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            $lockedSale = Sale::where('id', $sale->id)->lockForUpdate()->firstOrFail();

            if ($lockedSale->dispense_status !== 'pending') {
                return $lockedSale->load('salesLines');
            }

            $lines = SaleLine::where('sale_id', $lockedSale->id)
                ->lockForUpdate()
                ->get();

            $displayIds = $lines->pluck('product_display_id')->unique()->values();

            $displays = ProductDisplay::with('cell')
                ->whereIn('id', $displayIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $successCount = 0;
            $failedCount = 0;

            foreach ($lines as $line) {
                $display = $displays->get($line->product_display_id);
                $cell = $display?->cell;

                if (!$display || !$cell || (int) $cell->qty_current <= 0) {
                    $line->status = 'failed';
                    $line->save();
                    $failedCount++;
                    continue;
                }

                $cell->qty_current = max(0, (int) $cell->qty_current - 1);
                $cell->save();

                if ((int) $cell->qty_current === 0 && !$display->is_empty) {
                    $display->is_empty = true;
                    $display->save();
                }

                $line->status = 'success';
                $line->save();
                $successCount++;
            }

            if ($successCount > 0 && $failedCount === 0) {
                $lockedSale->dispense_status = 'success';
            } elseif ($successCount === 0) {
                $lockedSale->dispense_status = 'failed';
            } else {
                $lockedSale->dispense_status = 'partial';
            }

            $lockedSale->save();

            return $lockedSale->refresh()->load('salesLines');
        });
    }
}
