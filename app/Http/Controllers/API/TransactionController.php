<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\TransactionService;
use App\Services\MidtransQrisService;
use Exception;

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
                'message' => 'Failed to create transaction.',
                'error' => $e->getMessage(),
            ], 422);
        }

        $sale = $result['sale'];

        return response()->json([
            'data' => new SaleResource($sale),
            'payment' => $result['payment'],
            'is_duplicate' => $result['is_duplicate'],
            'message' => 'Transaction created successfully.',
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
        if (!$qrisService->verifySignature($request->all())) {
            return response()->json([
                'message' => 'Invalid Midtrans signature.',
            ], 403);
        }

        $sale = $transactionService->handleNotification($request->all());

        if (!$sale) {
            return response()->json([
                'message' => 'Transaction not found.',
            ], 404);
        }

        return response()->json([
            'data' => new SaleResource($sale),
            'message' => 'Notification processed.',
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
                'message' => 'Failed to sync payment status.',
                'error' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => new SaleResource($sale),
            'message' => 'Payment status synced.',
        ]);
    }

    public function cancel($id, TransactionService $transactionService, MidtransQrisService $qrisService)
    {
        $sale = Sale::with('salesLines')->findOrFail($id);

        try {
            $sale = $transactionService->cancelPendingPayment($sale, $qrisService);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel payment.',
                'error' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => new SaleResource($sale),
            'message' => 'Payment canceled.',
        ]);
    }
}
