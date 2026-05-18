<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRolePeneliti
{
    public function handle(Request $request, Closure $next): Response
    {
        // Menggunakan Spatie Laravel Permission
        if (!auth()->check() || !auth()->user()->hasRole('peneliti')) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Peneliti.');
        }

        return $next($request);
    }
}