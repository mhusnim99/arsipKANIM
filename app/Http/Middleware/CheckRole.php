<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Jika belum login, redirect ke login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Cek role user (sesuai field di database)
        $user = Auth::user();

        // Jika user memiliki field 'role' di database
        if ($user->role !== $role) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
