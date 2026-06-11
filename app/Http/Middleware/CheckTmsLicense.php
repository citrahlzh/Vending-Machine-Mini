<?php

namespace App\Http\Middleware;

use App\Models\TmsLicenseState;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTmsLicense
{
    public function handle(Request $request, Closure $next): Response
    {
        $state = TmsLicenseState::latest()->first();

        if (!$state) {
            return response()->json([
                'error' => 'Lisensi belum diverifikasi. Hubungi administrator.',
            ], 403);
        }

        if (in_array($state->status, ['revoked', 'expired', 'not_found'])) {
            return response()->json([
                'error'  => 'Lisensi tidak valid: ' . $state->status,
                'reason' => 'Mesin ini tidak dapat memproses transaksi.',
            ], 403);
        }

        return $next($request);
    }
}
