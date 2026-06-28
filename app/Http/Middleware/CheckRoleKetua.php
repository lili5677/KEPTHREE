<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleKetua
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Kalau pakai Spatie Permission
        if (method_exists($user, 'hasRole')) {
            if (!$user->hasRole('ketua')) {
                abort(403, 'Akses hanya untuk Ketua.');
            }

            return $next($request);
        }

        // Kalau role disimpan di kolom users.role
        if (($user->role ?? null) !== 'ketua') {
            abort(403, 'Akses hanya untuk Ketua.');
        }

        return $next($request);
    }
}