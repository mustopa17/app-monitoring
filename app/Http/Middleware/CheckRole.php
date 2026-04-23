<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! $request->user()) {
            return response()->json([
                'error' => 'Akses ditolak. Anda tidak memiliki izin.',
            ], 403);
        }

        // Cek menggunakan Spatie HasRoles
        if (! $request->user()->hasAnyRole($roles)) {
            return response()->json([
                'error' => 'Akses ditolak. Anda tidak memiliki izin.',
            ], 403);
        }

        return $next($request);
    }
}
