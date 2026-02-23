<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\TransactionService;
use App\Services\MidtransQrisService;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class TransactionController extends Controller
{
    public function checkout(Request $request, TransactionService $transactionService, MidtransQrisService $qrisService)
    {
        $request->validate([
            'idempotency_key' => 'sometimes|string|max:64',
            'items' => 'required|array|min:1',
            'items.*.product_display_id' => 'required|exists:product_displays,id',
            'items.*.qty' => 'required|integer|min:1|max:99',
        ]);

        try {
            $result = $transactionService->checkout($request, $qrisService);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat transaksi.',
                'error' => $e->getMessage(),
            ], 422);
        }

        $sale = $result['sale'];

        return response()->json([
            'data' => new SaleResource($sale),
            'payment' => $result['payment'],
            'is_duplicate' => $result['is_duplicate'],
            'message' => 'Transaksi berhasil dibuat.',
        ], 201);
    }

    public function notify(
        Request $request,
        TransactionService $transactionService,
        MidtransQrisService $qrisService
    )
    {
        return $this->handleMidtransWebhook($request, $transactionService, $qrisService);
    }

    public function webhook(
        Request $request,
        TransactionService $transactionService,
        MidtransQrisService $qrisService
    )
    {
        return $this->handleMidtransWebhook($request, $transactionService, $qrisService);
    }

    private function handleMidtransWebhook(
        Request $request,
        TransactionService $transactionService,
        MidtransQrisService $qrisService
    )
    {
        $payload = $request->all();

        Log::info('Midtrans webhook received', [
            'order_id' => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'payment_type' => $payload['payment_type'] ?? null,
            'fraud_status' => $payload['fraud_status'] ?? null,
        ]);

        if (!$qrisService->verifySignature($payload)) {
            Log::warning('Midtrans webhook rejected: invalid signature', [
                'order_id' => $payload['order_id'] ?? null,
                'status_code' => $payload['status_code'] ?? null,
            ]);

            return response()->json([
                'message' => 'Tanda tangan Midtrans tidak valid.',
            ], 403);
        }

        try {
            $sale = $transactionService->handleNotification($payload);
        } catch (Throwable $e) {
            Log::error('Midtrans webhook processing failed', [
                'order_id' => $payload['order_id'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal memproses notifikasi.',
            ], 500);
        }

        if (!$sale) {
            return response()->json([
                'message' => 'Webhook diterima, transaksi tidak ditemukan.',
            ]);
        }

        return response()->json([
            'data' => new SaleResource($sale),
            'message' => 'Notifikasi berhasil diproses.',
        ]);
    }

    public function show($id)
    {
        $sale = Sale::with('salesLines')->findOrFail($id);

        return new SaleResource($sale);
    }

    public function status($id, TransactionService $transactionService, MidtransQrisService $qrisService)
    {
        $sale = Sale::with('salesLines')->findOrFail($id);

        try {
            $sale = $transactionService->syncPaymentStatus($sale, $qrisService);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal sinkronisasi status pembayaran.',
                'error' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => new SaleResource($sale),
            'message' => 'Status pembayaran berhasil disinkronkan.',
        ]);
    }

    public function cancel($id, TransactionService $transactionService, MidtransQrisService $qrisService)
    {
        $sale = Sale::with('salesLines')->findOrFail($id);

        try {
            $sale = $transactionService->cancelPendingPayment($sale, $qrisService);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal membatalkan pembayaran.',
                'error' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => new SaleResource($sale),
            'message' => 'Pembayaran berhasil dibatalkan.',
        ]);
    }
}
