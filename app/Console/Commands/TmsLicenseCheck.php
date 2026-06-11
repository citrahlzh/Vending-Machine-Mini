<?php

namespace App\Console\Commands;

use App\Services\TMS\LicenseService;
use Illuminate\Console\Command;

class TmsLicenseCheck extends Command
{
    protected $signature   = 'tms:license-check';
    protected $description = 'Verifikasi lisensi lokal dan online ke TMS';

    public function handle(LicenseService $service): void
    {
        $this->info('[TMS] Memeriksa lisensi...');
        $valid = $service->check();
        $this->info($valid ? '[TMS] Lisensi valid.' : '[TMS] ⚠️ Lisensi TIDAK valid — lockdown diaktifkan.');
    }
}
