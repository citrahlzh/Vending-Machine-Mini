<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;

class MidtransQrisService
{
    public function __construct()
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

        return CoreApi::charge($payload);
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
        return Transaction::status($orderId);
    }

    public function cancel(string $orderId)
    {
        return Transaction::cancel($orderId);
    }

    public function expire(string $orderId)
    {
        return Transaction::expire($orderId);
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
