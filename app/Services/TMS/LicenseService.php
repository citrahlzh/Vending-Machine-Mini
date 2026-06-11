<?php

namespace App\Services\TMS;

use App\Models\TmsLicenseState;
use App\Services\TMS\LockdownHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    public function check(): bool
    {
        // Lapis 1: verifikasi lokal
        if (!$this->verifyLocal()) {
            app(LockdownHandler::class)->lock('license_invalid_local');
            return false;
        }

        $state = TmsLicenseState::latest()->first();

        // Lapis 3: grace period — jika TMS tidak bisa dihubungi
        if ($state && $state->withinGracePeriod()) {
            Log::info('[TMS] Lisensi dalam grace period. Lewati verifikasi online.');
            return true;
        }

        // Lapis 2: verifikasi online
        return $this->verifyOnline();
    }

    private function verifyLocal(): bool
    {
        $licensePath = config('tms.license_path');

        if (!file_exists($licensePath)) {
            Log::error('[TMS] File license.dat tidak ditemukan di: ' . $licensePath);
            return false;
        }

        try {
            $fileContent = file_get_contents($licensePath);
            $data        = json_decode(base64_decode($fileContent), true);

            if (!$data || !isset($data['payload'], $data['signature'])) {
                Log::error('[TMS] Format license.dat tidak valid.');
                return false;
            }

            // Verifikasi RSA signature menggunakan public key TMS
            $publicKeyPath = storage_path('keys/tms_public.pem');
            $publicKey     = openssl_get_publickey(file_get_contents($publicKeyPath));
            $signature     = base64_decode($data['signature']);
            $verified      = openssl_verify($data['payload'], $signature, $publicKey, OPENSSL_ALGO_SHA256);

            if ($verified !== 1) {
                Log::error('[TMS] Signature lisensi tidak valid.');
                return false;
            }

            // Decrypt payload AES-256-CBC
            $decrypted = openssl_decrypt(
                base64_decode($data['payload']),
                'AES-256-CBC',
                config('tms.api_key'), // AES key dari api_key
                0,
                base64_decode($data['iv'] ?? '')
            );

            $payload = json_decode($decrypted, true);

            // Cek expiry
            if (isset($payload['expires_at']) && now()->toDateString() > $payload['expires_at']) {
                Log::error('[TMS] Lisensi sudah expired: ' . $payload['expires_at']);
                return false;
            }

            // Cek machine_code cocok
            if (($payload['machine_code'] ?? '') !== config('tms.machine_code')) {
                Log::error('[TMS] machine_code di lisensi tidak cocok.');
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('[TMS] Verifikasi lisensi lokal exception: ' . $e->getMessage());
            return false;
        }
    }

    private function verifyOnline(): bool
    {
        try {
            $licensePath = config('tms.license_path');
            $hash        = hash_file('sha256', $licensePath);

            $response = Http::timeout(10)
                ->withHeaders(['X-Machine-Key' => config('tms.api_key')])
                ->get(config('tms.base_url') . '/api/vm/license/verify', [
                    'hash'         => $hash,
                    'machine_code' => config('tms.machine_code'),
                ]);

            if ($response->failed()) {
                // Network error = grace period, bukan lockdown
                Log::warning('[TMS] Verifikasi online gagal (network error). Gunakan grace period.');
                return true;
            }

            $result     = $response->json('result');
            $expiresAt  = $response->json('expires_at');

            // Simpan state ke DB
            TmsLicenseState::create([
                'file_hash'          => $hash,
                'status'             => $result === 'valid' ? 'valid' : $result,
                'verified_at'        => now(),
                'license_expires_at' => $expiresAt,
            ]);

            if (in_array($result, ['revoked', 'expired', 'not_found'])) {
                Log::error("[TMS] Lisensi ditolak oleh server: {$result}");
                app(LockdownHandler::class)->lock($result);
                return false;
            }

            Log::info('[TMS] Verifikasi lisensi online: ' . $result);
            return true;
        } catch (\Throwable $e) {
            Log::error('[TMS] Verifikasi online exception: ' . $e->getMessage());
            return true; // Gagal koneksi = grace period
        }
    }
}
