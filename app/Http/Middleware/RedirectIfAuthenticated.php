<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // User sudah login, redirect berdasarkan role
                $user = Auth::guard($guard)->user();

                // Pastikan user punya role
                if (isset($user->role) && $user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                // Default ke user pengiriman
                return redirect()->route('user.pengiriman');
            }
        }

        return $next($request);
    }
}
