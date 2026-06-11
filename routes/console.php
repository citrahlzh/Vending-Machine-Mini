<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('transactions:sync-pending --limit=50')
    ->everyTenSeconds()
    ->withoutOverlapping();

// Heartbeat ke TMS — setiap menit
Schedule::command('tms:heartbeat')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Eksekusi command dari antrian — setiap menit
Schedule::command('tms:execute-commands')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Retry push queue gagal — setiap 5 menit
Schedule::command('tms:retry-push')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Push summary transaksi harian
Schedule::command('tms:push-transactions')
    ->dailyAt('23:55')
    ->withoutOverlapping();

// Cek lisensi
Schedule::command('tms:license-check')
    ->everySixHours()
    ->withoutOverlapping();
