<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->auditLogService->logAuthEvent('auth.login.failed', null, $request, [
                'username' => $request->username,
                'guard' => 'sanctum',
            ], 401);

            return response()->json([
                'message' => 'Login gagal'
            ], 401);
        }

        $token = $user->createToken('vending-machine')->plainTextToken;
        $this->auditLogService->logAuthEvent('auth.login.success', $user, $request, [
            'username' => $user->username,
            'guard' => 'sanctum',
        ], 200);

        return response()->json([
            'token' => $token
        ]);
    }
}
