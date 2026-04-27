<?php

namespace App\Services;

use App\Jobs\ProcessSaleDispense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\ProductDisplay;
use App\Models\Reward;
use App\Services\MidtransQrisService;
use App\Services\SystemNotificationService;
use App\Services\VendingDispenseService;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Exception;

class TransactionService
{
    private const LOW_STOCK_THRESHOLD = 3;
    private const DEFAULT_IDEMPOTENCY_KEY_LENGTH = 12;
    private const MIN_IDEMPOTENCY_KEY_LENGTH = 6;
    private const MAX_IDEMPOTENCY_KEY_LENGTH = 64;

    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly SystemNotificationService $notificationService,
        private readonly VendingDispenseService $vendingDispenseService
    ) {
    }

    public function checkout(Request $request, MidtransQrisService $qrisService): array
    {
        $items = collect($request->input('items', []));
        $orderId = $request->input('idempotency_key');
        $orderId = is_string($orderId) && $orderId !== '' ? $orderId : $this->generateUniqueIdempotencyKey();

        $existing = Sale::with('salesLines')->where('idempotency_key', $orderId)->first();
        if ($existing) {
            $this->auditLogService->logBusinessEvent(
                'transaction.checkout.duplicate',
                "Permintaan checkout duplikat untuk transaksi {$existing->idempotency_key}.",
                [
                    'sale_id' => $existing->id,
                    'idempotency_key' => $existing->idempotency_key,
                ],
                $request->user(),
                $existing
            );

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

            $productDisplays = ProductDisplay::with(['price', 'cell', 'product'])
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

                $price = (int) $display->price->price;

                // $totalAmount += ((int) $display->price->price) * $qty;
                $totalAmount += $price * $qty;
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

                $display = $productDisplays->get((int) $displayId);

                $price = (int) $display->price->price;
                $productName = $display->product->product_name ?? 'Unknown Product';
                $cellCode = $display->cell->code ?? null;

                $qty = (int) $qty;

                for ($i = 0; $i < $qty; $i++) {

                    SaleLine::create([
                        'sale_id' => $sale->id,
                        'product_display_id' => (int) $displayId,

                        // snapshot data
                        'product_name' => $productName,
                        'cell_code' => $cellCode,
                        'price' => $price,

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
            $this->auditLogService->logBusinessEvent(
                'transaction.checkout.failed',
                "Pembuatan pembayaran untuk transaksi {$sale->idempotency_key} gagal.",
                [
                    'sale_id' => $sale->id,
                    'error' => $e->getMessage(),
                ],
                $request->user(),
                $sale
            );
            throw $e;
        }

        $sale->update([
            'qris_id' => $charge->transaction_id ?? $sale->idempotency_key,
        ]);

        $this->notifyTransactionCreated($sale->refresh()->load('salesLines'));
        $this->auditLogService->logBusinessEvent(
            'transaction.checkout.created',
            "Transaksi {$sale->idempotency_key} berhasil dibuat.",
            [
                'sale_id' => $sale->id,
                'total_amount' => $sale->total_amount,
                'item_count' => $sale->salesLines->count(),
            ],
            $request->user(),
            $sale
        );

        return [
            'sale' => $sale->refresh()->load('salesLines'),
            'payment' => $qrisService->extractQrisData($charge),
            'is_duplicate' => false,
        ];
    }

    private function generateUniqueIdempotencyKey(): string
    {
        $vm_code = SiteSetting::where('key', 'machine_code')->value('value') ?? 'VM-XXX';
        $split_code = explode('-', $vm_code);
        $prefix = count($split_code) > 1 ? strtoupper($split_code[1]) : 'VM';

        $year = Carbon::now()->format('y');
        $month = Carbon::now()->format('m');
        $date = Carbon::now()->format('d');

        $randomDigits = random_int(1, 999999);

        $candidate = "{$prefix}{$year}{$month}{$date}{$randomDigits}";
        // $length = (int) config('app.transaction_idempotency_key_length', self::DEFAULT_IDEMPOTENCY_KEY_LENGTH);
        // $length = max(self::MIN_IDEMPOTENCY_KEY_LENGTH, min($length, self::MAX_IDEMPOTENCY_KEY_LENGTH));

        // do {
        //     $candidate = $this->randomUppercaseAlphanumeric($length);
        // } while (Sale::where('idempotency_key', $candidate)->exists());

        return $candidate;
    }

    public function handleNotification(array $payload): ?Sale
    {
        $orderId = $payload['order_id'] ?? null;
        if (!$orderId) {
            $this->auditLogService->logBusinessEvent(
                'payment.webhook.ignored',
                'Webhook payment diabaikan karena order_id tidak ada.',
                [
                    'provider' => 'midtrans',
                    'payload' => $payload,
                ]
            );

            return null;
        }

        $sale = Sale::with('salesLines')->where('idempotency_key', $orderId)->first();
        if (!$sale) {
            $this->auditLogService->logBusinessEvent(
                'payment.webhook.unmatched',
                "Webhook payment diterima tetapi transaksi {$orderId} tidak ditemukan.",
                [
                    'provider' => 'midtrans',
                    'order_id' => $orderId,
                    'payload' => $payload,
                ]
            );

            return null;
        }

        $mapped = $this->mapMidtransStatus($payload['transaction_status'] ?? null) ?? $sale->status;

        $this->auditLogService->logBusinessEvent(
            'payment.webhook.processed',
            "Webhook payment diproses untuk order {$orderId}.",
            [
                'provider' => 'midtrans',
                'sale_id' => $sale->id,
                'order_id' => $orderId,
                'transaction_id' => $payload['transaction_id'] ?? null,
                'transaction_status' => $payload['transaction_status'] ?? null,
                'mapped_status' => $mapped,
                'payload' => $payload,
            ],
            null,
            $sale
        );

        return $this->applySaleStatus(
            $sale,
            $mapped,
            $payload['transaction_id'] ?? null
        );
    }

    public function handleMqttNotification(array $payload): ?Sale
    {
        $normalized = $this->normalizeMqttPaymentPayload($payload);

        if (!$normalized) {
            $this->auditLogService->logBusinessEvent(
                'payment.mqtt.ignored',
                'Notifikasi payment MQTT diabaikan karena payload tidak dikenali.',
                [
                    'provider' => 'midtrans',
                    'source' => 'mqtt',
                    'payload' => $payload,
                ]
            );

            return null;
        }

        $this->auditLogService->logBusinessEvent(
            'payment.mqtt.received',
            "Notifikasi payment MQTT diterima untuk order {$normalized['order_id']}.",
            [
                'provider' => 'midtrans',
                'source' => 'mqtt',
                'payload' => $payload,
                'normalized_payload' => $normalized,
            ]
        );

        return $this->handleNotification($normalized);
    }

    public function syncPaymentStatus(Sale $sale, MidtransQrisService $qrisService): Sale
    {
        $status = $qrisService->status($sale->idempotency_key);

        $mapped = $this->mapMidtransStatus(data_get($status, 'transaction_status')) ?? $sale->status;

        $this->auditLogService->logBusinessEvent(
            'payment.sync.processed',
            "Sinkronisasi payment diproses untuk order {$sale->idempotency_key}.",
            [
                'provider' => 'midtrans',
                'sale_id' => $sale->id,
                'order_id' => $sale->idempotency_key,
                'transaction_id' => data_get($status, 'transaction_id'),
                'transaction_status' => data_get($status, 'transaction_status'),
                'mapped_status' => $mapped,
                'response' => $status,
            ],
            null,
            $sale
        );

        return $this->applySaleStatus(
            $sale,
            $mapped,
            data_get($status, 'transaction_id')
        );
    }

    public function syncPendingPayments(MidtransQrisService $qrisService, int $limit = 50): array
    {
        $pendingSales = Sale::query()
            ->where('status', 'pending')
            ->orderBy('transaction_date')
            ->limit(max(1, $limit))
            ->get();

        $checked = 0;
        $updated = 0;
        $failed = 0;

        foreach ($pendingSales as $sale) {
            $checked++;

            try {
                $previousStatus = $sale->status;
                $sale = $this->syncPaymentStatus($sale, $qrisService);

                if ($sale->status !== $previousStatus) {
                    $updated++;
                }
            } catch (Exception $e) {
                $failed++;
                Log::warning('Failed syncing pending Midtrans payment', [
                    'sale_id' => $sale->id,
                    'idempotency_key' => $sale->idempotency_key,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'checked' => $checked,
            'updated' => $updated,
            'failed' => $failed,
        ];
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
            $this->auditLogService->logBusinessEvent(
                'payment.cancel.skipped',
                "Cancel payment dilewati untuk order {$sale->idempotency_key} karena status akhir sudah tercapai.",
                [
                    'provider' => 'midtrans',
                    'sale_id' => $sale->id,
                    'order_id' => $sale->idempotency_key,
                    'status' => $sale->status,
                ],
                null,
                $sale
            );

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

        $this->auditLogService->logBusinessEvent(
            'payment.cancel.processed',
            "Cancel payment diproses untuk order {$sale->idempotency_key}.",
            [
                'provider' => 'midtrans',
                'sale_id' => $sale->id,
                'order_id' => $sale->idempotency_key,
            ],
            null,
            $sale
        );

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

    private function normalizeMqttPaymentPayload(array $payload): ?array
    {
        $orderId = data_get($payload, 'order_id')
            ?? data_get($payload, 'transaction_details.order_id')
            ?? data_get($payload, 'data.order_id')
            ?? data_get($payload, 'payload.order_id');

        $transactionStatus = data_get($payload, 'transaction_status')
            ?? data_get($payload, 'transaction.transaction_status')
            ?? data_get($payload, 'status')
            ?? data_get($payload, 'payment_status')
            ?? data_get($payload, 'data.transaction_status')
            ?? data_get($payload, 'payload.transaction_status');

        if (!$orderId || !$transactionStatus) {
            return null;
        }

        $grossAmount = data_get($payload, 'gross_amount')
            ?? data_get($payload, 'transaction.gross_amount')
            ?? data_get($payload, 'transaction_details.gross_amount')
            ?? data_get($payload, 'data.gross_amount')
            ?? 0;

        $transactionId = data_get($payload, 'transaction_id')
            ?? data_get($payload, 'transaction.transaction_id')
            ?? data_get($payload, 'data.transaction_id')
            ?? (string) $orderId;

        $paymentType = data_get($payload, 'payment_type')
            ?? data_get($payload, 'transaction.payment_type')
            ?? data_get($payload, 'data.payment_type')
            ?? 'qris';

        $statusCode = data_get($payload, 'status_code')
            ?? data_get($payload, 'transaction.status_code')
            ?? data_get($payload, 'data.status_code')
            ?? '200';

        return [
            'order_id' => (string) $orderId,
            'transaction_id' => (string) $transactionId,
            'transaction_status' => (string) $transactionStatus,
            'payment_type' => (string) $paymentType,
            'gross_amount' => (string) $grossAmount,
            'status_code' => (string) $statusCode,
            'source' => 'mqtt',
            'raw_payload' => $payload,
        ];
    }

    private function applySaleStatus(Sale $sale, string $status, ?string $transactionId): Sale
    {
        $previousStatus = $sale->status;
        $previousDispenseStatus = $sale->dispense_status;
        $status = $this->resolveNextStatus($previousStatus, $status);

        $sale->status = $status;
        if (!empty($transactionId)) {
            $sale->qris_id = $transactionId;
        }
        $sale->save();

        if ($status === 'paid') {
            $updatedSale = $sale->refresh()->load('salesLines');
            if ($previousStatus !== 'paid' && $updatedSale->dispense_status === 'pending') {
                $updatedSale = $this->finalizeDispense($updatedSale);
                ProcessSaleDispense::dispatchAfterResponse($updatedSale->id);
            }

            $this->notifyStockAfterSuccessfulDispense($updatedSale, $previousDispenseStatus);
            $this->notifyTransactionUpdate($updatedSale, $previousStatus, $previousDispenseStatus);
            $this->logStatusTransition($updatedSale, $previousStatus, $previousDispenseStatus);

            return $updatedSale;
        }

        if (in_array($status, ['failed', 'expired'], true)) {
            $sale->salesLines()->update(['status' => 'failed']);
            if ($sale->dispense_status === 'pending') {
                $sale->dispense_status = 'failed';
                $sale->save();
            }
        }

        $updatedSale = $sale->refresh()->load('salesLines');
        $this->notifyTransactionUpdate($updatedSale, $previousStatus, $previousDispenseStatus);
        $this->logStatusTransition($updatedSale, $previousStatus, $previousDispenseStatus);

        return $updatedSale;
    }

    private function resolveNextStatus(string $currentStatus, string $incomingStatus): string
    {
        if ($currentStatus === 'paid' && $incomingStatus !== 'paid') {
            return 'paid';
        }

        if (in_array($currentStatus, ['failed', 'expired'], true) && $incomingStatus === 'pending') {
            return $currentStatus;
        }

        return $incomingStatus;
    }

    public function processHardwareDispenseForSale(int $saleId): void
    {
        $sale = Sale::with(['salesLines.productDisplay.cell'])->find($saleId);
        if (!$sale || $sale->status !== 'paid') {
            return;
        }

        foreach ($sale->salesLines as $line) {
            if ($line->status !== 'success') {
                continue;
            }

            // $cellCode = (string) optional(optional($line->productDisplay)->cell)->code;
            $cellCode = $line->cell_code;
            if ($cellCode === '') {
                $this->auditLogService->logBusinessEvent(
                    'dispense.skipped',
                    "Dispense transaksi {$sale->idempotency_key} dilewati karena cell kosong.",
                    [
                        'sale_id' => $sale->id,
                        'sale_line_id' => $line->id,
                        'transaction_id' => $sale->idempotency_key,
                    ],
                    null,
                    $sale
                );
                continue;
            }

            $this->vendingDispenseService->dispense(
                (string) $sale->idempotency_key,
                $cellCode,
                [
                    'source' => 'sale',
                    'sale_id' => $sale->id,
                    'sale_line_id' => $line->id,
                ]
            );
        }
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

            $displays = ProductDisplay::with(['cell', 'product'])
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

                    Reward::where('product_display_id', $display->id)
                        ->where('is_active', true)
                        ->update(['is_active' => false]);
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

    private function notifyTransactionCreated(Sale $sale): void
    {
        $itemCount = $sale->salesLines->count();

        $this->notificationService->notifyActiveUsers(
            title: "Transaksi baru #{$sale->id}",
            message: "Ada transaksi baru dengan {$itemCount} item. Menunggu pembayaran.",
            type: 'info',
            actionUrl: route('dashboard.transactions.show', ['id' => $sale->id]),
            meta: [
                'sale_id' => $sale->id,
                'status' => $sale->status,
                'dispense_status' => $sale->dispense_status,
                'event' => 'transaction_created',
            ]
        );
    }

    private function notifyStockAfterSuccessfulDispense(Sale $sale, string $previousDispenseStatus): void
    {
        if ($previousDispenseStatus !== 'pending') {
            return;
        }

        if (!in_array($sale->dispense_status, ['success', 'partial'], true)) {
            return;
        }

        $successDisplayIds = $sale->salesLines
            ->where('status', 'success')
            ->pluck('product_display_id')
            ->unique()
            ->values();

        if ($successDisplayIds->isEmpty()) {
            return;
        }

        $affectedDisplays = ProductDisplay::with(['product', 'cell'])
            ->whereIn('id', $successDisplayIds)
            ->get();

        $outOfStockNames = [];
        $lowStockNames = [];

        foreach ($affectedDisplays as $display) {
            $stock = (int) optional($display->cell)->qty_current;
            $productName = optional($display->product)->product_name ?? "Produk #{$display->id}";

            if ($stock <= 0 || (bool) $display->is_empty) {
                $outOfStockNames[] = $productName;
                continue;
            }

            if ($stock <= self::LOW_STOCK_THRESHOLD) {
                $lowStockNames[] = "{$productName} (sisa {$stock})";
            }
        }

        if (!empty($outOfStockNames)) {
            $this->notificationService->notifyActiveUsers(
                title: 'Stok habis setelah transaksi',
                message: 'Produk habis: ' . implode(', ', $outOfStockNames) . '.',
                type: 'warning',
                actionUrl: route('dashboard.product-displays.index'),
                meta: [
                    'sale_id' => $sale->id,
                    'event' => 'stock_out_after_dispense',
                    'products' => $outOfStockNames,
                ]
            );
        }

        if (!empty($lowStockNames)) {
            $this->notificationService->notifyActiveUsers(
                title: 'Stok menipis setelah transaksi',
                message: 'Produk menipis: ' . implode(', ', $lowStockNames) . '.',
                type: 'warning',
                actionUrl: route('dashboard.product-displays.index'),
                meta: [
                    'sale_id' => $sale->id,
                    'event' => 'low_stock_after_dispense',
                    'products' => $lowStockNames,
                ]
            );
        }
    }

    private function notifyTransactionUpdate(
        Sale $sale,
        string $previousStatus,
        string $previousDispenseStatus
    ): void {
        $statusChanged = $sale->status !== $previousStatus;
        $dispenseChanged = $sale->dispense_status !== $previousDispenseStatus;

        if (!$statusChanged && !$dispenseChanged) {
            return;
        }

        $statusText = match ($sale->status) {
            'paid' => 'Pembayaran berhasil',
            'pending' => 'Menunggu pembayaran',
            'failed' => 'Pembayaran gagal',
            'expired' => 'Pembayaran kedaluwarsa',
            default => 'Status transaksi diperbarui',
        };

        $dispenseText = match ($sale->dispense_status) {
            'success' => 'Produk berhasil dikeluarkan.',
            'partial' => 'Sebagian produk gagal dikeluarkan.',
            'failed' => 'Gagal mengeluarkan produk.',
            default => 'Menunggu proses pengeluaran produk.',
        };

        $this->notificationService->notifyActiveUsers(
            title: "Transaksi #{$sale->id}",
            message: "{$statusText}. {$dispenseText}",
            type: $sale->status === 'paid' ? 'success' : ($sale->status === 'pending' ? 'info' : 'warning'),
            actionUrl: route('dashboard.transactions.show', ['id' => $sale->id]),
            meta: [
                'sale_id' => $sale->id,
                'status' => $sale->status,
                'dispense_status' => $sale->dispense_status,
            ]
        );
    }

    private function logStatusTransition(Sale $sale, string $previousStatus, string $previousDispenseStatus): void
    {
        if ($sale->status === $previousStatus && $sale->dispense_status === $previousDispenseStatus) {
            return;
        }

        $this->auditLogService->logBusinessEvent(
            'transaction.status.updated',
            "Status transaksi {$sale->idempotency_key} berubah.",
            [
                'sale_id' => $sale->id,
                'previous_status' => $previousStatus,
                'current_status' => $sale->status,
                'previous_dispense_status' => $previousDispenseStatus,
                'current_dispense_status' => $sale->dispense_status,
            ],
            null,
            $sale
        );
    }
}
