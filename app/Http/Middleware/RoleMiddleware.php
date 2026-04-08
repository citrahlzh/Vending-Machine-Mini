<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (empty($roles)) {
            return $next($request);
        }

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $slug = $user->role?->slug;
        if (!$slug || !in_array($slug, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
