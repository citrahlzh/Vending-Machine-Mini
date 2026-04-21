<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;
use Throwable;

class MidtransQrisService
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    )
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.sanitize');
        Config::$is3ds = (bool) config('midtrans.3ds');
    }

    public function charge(string $orderId, int $grossAmount, int $expiryMinutes = 5)
    {
        $payload = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            // biasanya bisa dipakai untuk atur expiry (sering dipakai pada e-wallet/QRIS flow)
            'custom_expiry' => [
                'expiry_duration' => $expiryMinutes,
                'unit' => 'minute',
            ],
        ];

        $this->auditLogService->logBusinessEvent(
            'payment.charge.requested',
            "Request charge Midtrans dibuat untuk order {$orderId}.",
            [
                'provider' => 'midtrans',
                'payment_type' => 'qris',
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'expiry_minutes' => $expiryMinutes,
                'payload' => $payload,
            ]
        );

        try {
            $response = CoreApi::charge($payload);

            $this->auditLogService->logBusinessEvent(
                'payment.charge.succeeded',
                "Charge Midtrans berhasil dibuat untuk order {$orderId}.",
                [
                    'provider' => 'midtrans',
                    'payment_type' => 'qris',
                    'order_id' => $orderId,
                    'transaction_id' => data_get($response, 'transaction_id'),
                    'transaction_status' => data_get($response, 'transaction_status'),
                    'gross_amount' => data_get($response, 'gross_amount'),
                    'expiry_time' => data_get($response, 'expiry_time'),
                    'response' => $response,
                ]
            );

            return $response;
        } catch (Throwable $throwable) {
            $this->auditLogService->logBusinessEvent(
                'payment.charge.failed',
                "Charge Midtrans gagal untuk order {$orderId}.",
                [
                    'provider' => 'midtrans',
                    'payment_type' => 'qris',
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                    'error' => $throwable->getMessage(),
                ]
            );

            throw $throwable;
        }
    }

    public function verifySignature(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return false;
        }

        $computed = hash('sha512', $orderId . $statusCode . $grossAmount . Config::$serverKey);

        return hash_equals($computed, $signatureKey);
    }

    public function status(string $orderId)
    {
        $this->auditLogService->logBusinessEvent(
            'payment.status.requested',
            "Cek status Midtrans diminta untuk order {$orderId}.",
            [
                'provider' => 'midtrans',
                'order_id' => $orderId,
            ]
        );

        try {
            $response = Transaction::status($orderId);

            $this->auditLogService->logBusinessEvent(
                'payment.status.received',
                "Status Midtrans diterima untuk order {$orderId}.",
                [
                    'provider' => 'midtrans',
                    'order_id' => $orderId,
                    'transaction_id' => data_get($response, 'transaction_id'),
                    'transaction_status' => data_get($response, 'transaction_status'),
                    'response' => $response,
                ]
            );

            return $response;
        } catch (Throwable $throwable) {
            $this->auditLogService->logBusinessEvent(
                'payment.status.failed',
                "Cek status Midtrans gagal untuk order {$orderId}.",
                [
                    'provider' => 'midtrans',
                    'order_id' => $orderId,
                    'error' => $throwable->getMessage(),
                ]
            );

            throw $throwable;
        }
    }

    public function cancel(string $orderId)
    {
        $this->auditLogService->logBusinessEvent(
            'payment.cancel.requested',
            "Cancel Midtrans diminta untuk order {$orderId}.",
            [
                'provider' => 'midtrans',
                'order_id' => $orderId,
            ]
        );

        try {
            $response = Transaction::cancel($orderId);

            $this->auditLogService->logBusinessEvent(
                'payment.cancel.succeeded',
                "Cancel Midtrans berhasil untuk order {$orderId}.",
                [
                    'provider' => 'midtrans',
                    'order_id' => $orderId,
                    'response' => $response,
                ]
            );

            return $response;
        } catch (Throwable $throwable) {
            $this->auditLogService->logBusinessEvent(
                'payment.cancel.failed',
                "Cancel Midtrans gagal untuk order {$orderId}.",
                [
                    'provider' => 'midtrans',
                    'order_id' => $orderId,
                    'error' => $throwable->getMessage(),
                ]
            );

            throw $throwable;
        }
    }

    public function expire(string $orderId)
    {
        $this->auditLogService->logBusinessEvent(
            'payment.expire.requested',
            "Expire Midtrans diminta untuk order {$orderId}.",
            [
                'provider' => 'midtrans',
                'order_id' => $orderId,
            ]
        );

        try {
            $response = Transaction::expire($orderId);

            $this->auditLogService->logBusinessEvent(
                'payment.expire.succeeded',
                "Expire Midtrans berhasil untuk order {$orderId}.",
                [
                    'provider' => 'midtrans',
                    'order_id' => $orderId,
                    'response' => $response,
                ]
            );

            return $response;
        } catch (Throwable $throwable) {
            $this->auditLogService->logBusinessEvent(
                'payment.expire.failed',
                "Expire Midtrans gagal untuk order {$orderId}.",
                [
                    'provider' => 'midtrans',
                    'order_id' => $orderId,
                    'error' => $throwable->getMessage(),
                ]
            );

            throw $throwable;
        }
    }

    public function extractQrisData($charge): array
    {
        $actions = data_get($charge, 'actions', []);
        $qrUrl = $this->findActionUrl($actions, ['generate-qr-code', 'qr-code', 'qris']);

        return [
            'transaction_id' => data_get($charge, 'transaction_id'),
            'order_id' => data_get($charge, 'order_id'),
            'gross_amount' => data_get($charge, 'gross_amount'),
            'expiry_time' => data_get($charge, 'expiry_time'),
            'qr_url' => $qrUrl,
            'qr_string' => data_get($charge, 'qr_string'),
        ];
    }

    private function findActionUrl($actions, array $names): ?string
    {
        foreach ($actions as $action) {
            $name = data_get($action, 'name');
            if ($name && in_array($name, $names, true)) {
                return data_get($action, 'url');
            }
        }

        return null;
    }
}
