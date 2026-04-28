<?php

namespace App\Services;

use OpenSSLAsymmetricKey;

class LicenseService
{
    public function getUsbSerial(): ?string
    {
        $cmd = 'powershell "Get-CimInstance Win32_DiskDrive | Where-Object {$_.InterfaceType -eq \'USB\'} | Select-Object -ExpandProperty SerialNumber"';

        $output = trim(shell_exec($cmd));

        if (!$output) return null;

        // ambil baris pertama saja (hindari multiple device)
        $lines = explode("\n", $output);

        return trim($lines[0]);
    }

    public function findLicense(): ?string
    {
        foreach (range('D', 'Z') as $drive) {
            $pathJson = $drive . ':/license.json';
            $pathDat  = $drive . ':/license.dat';

            if (file_exists($pathDat)) return $pathDat;
            if (file_exists($pathJson)) return $pathJson;
        }

        return null;
    }

    protected function decryptIfNeeded(string $path): ?array
    {
        // kalau pakai .dat → decrypt
        if (str_ends_with($path, '.dat')) {

            $key = config('license.aes_key');

            $raw = base64_decode(file_get_contents($path));
            if (!$raw) return null;

            $iv = substr($raw, 0, 16);
            $encrypted = substr($raw, 16);

            $json = openssl_decrypt(
                $encrypted,
                'AES-256-CBC',
                $key,
                0,
                $iv
            );

            return json_decode($json, true);
        }

        // kalau json biasa
        return json_decode(file_get_contents($path), true);
    }

    public function isValid(): bool
    {
        $path = $this->findLicense();
        if (!$path) return false;

        $license = $this->decryptIfNeeded($path);

        if (!is_array($license) || !isset($license['data'], $license['signature'])) {
            return false;
        }

        // validasi field wajib
        if (!isset(
            $license['data']['usb_serial'],
            $license['data']['expired']
        )) {
            return false;
        }

        $data = json_encode($license['data']);
        $signature = base64_decode($license['signature']);

        if (!$data || !$signature) return false;

        $publicKey = $this->resolvePublicKey();
        if (!$publicKey) return false;

        // 🔐 VERIFY SIGNATURE
        if (openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256) !== 1) {
            return false;
        }

        // 🔌 USB CHECK
        $usb = $this->getUsbSerial();
        if (!$usb || $license['data']['usb_serial'] !== $usb) {
            return false;
        }

        // ⏰ EXPIRED CHECK
        if (strtotime($license['data']['expired']) < time()) {
            return false;
        }

        return true;
    }

    protected function resolvePublicKey(): OpenSSLAsymmetricKey|false
    {
        $configuredKey = config('license.public_key');

        if (is_string($configuredKey) && trim($configuredKey) !== '') {
            $key = openssl_pkey_get_public($configuredKey);
            if ($key !== false) return $key;
        }

        $path = config('license.public_key_path');

        if (!is_string($path) || !is_file($path)) {
            return false;
        }

        return openssl_pkey_get_public(file_get_contents($path));
    }
}
