<?php

namespace App\Providers;

use App\Services\TMS\LicenseService;
use Illuminate\Support\ServiceProvider;

class TmsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Jalankan license check saat startup (hanya jika bukan CLI/artisan)
        if (!$this->app->runningInConsole()) {
            try {
                $this->app->make(LicenseService::class)->check();
            } catch (\Throwable $e) {
                // Jangan crash startup — log saja
                logger()->error('[TMS] Startup license check gagal: ' . $e->getMessage());
            }
        }
    }
}
