<?php

namespace App\Http\Middleware;

use Closure;

class CheckLicense
{
    public function handle($request, Closure $next)
    {
        if (!app(\App\Services\LicenseService::class)->isValid()) {
            abort(403, 'License tidak valid');
        }

        return $next($request);
    }
}
