<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRoleAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Admin.');
        }

        return $next($request);
    }
}