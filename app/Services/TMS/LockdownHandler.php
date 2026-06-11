<?php

namespace App\Services\TMS;

use Illuminate\Support\Facades\Log;

class LockdownHandler
{
    public function lock(string $reason): void
    {
        Log::critical("[TMS] LOCKDOWN diaktifkan. Alasan: {$reason}");

        // Simpan state lockdown ke cache/storage agar bisa dibaca middleware
        cache()->forever('tms_lockdown', [
            'active'     => true,
            'reason'     => $reason,
            'locked_at'  => now()->toIso8601String(),
        ]);
    }

    public function isLocked(): bool
    {
        return cache()->get('tms_lockdown.active', false);
    }

    public function unlock(): void
    {
        cache()->forget('tms_lockdown');
        Log::info('[TMS] Lockdown dinonaktifkan.');
    }
}
