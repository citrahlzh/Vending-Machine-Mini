<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->input('remember', false);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password'], 'is_active' => 1], $remember)) {
            $request->session()->regenerate();
            $this->auditLogService->logAuthEvent('auth.login.success', Auth::user(), $request, [
                'username' => $credentials['username'],
                'remember' => $remember,
            ], 200);

            return redirect()->intended(route('dashboard.index'));
        }

        $this->auditLogService->logAuthEvent('auth.login.failed', null, $request, [
            'username' => $credentials['username'],
            'remember' => $remember,
        ], 422);

        return back()->withErrors([
            'username' => 'Username atau kata sandi salah, atau akun belum aktif.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $this->auditLogService->logAuthEvent('auth.logout', $user, $request, [], 200);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
