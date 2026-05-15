<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();

        // Superadmin selalu punya akses ke semuanya
        if ($user->role === \App\Enums\UserRole::SUPERADMIN) {
            return $next($request);
        }

        // Cek apakah role user ada dalam daftar role yang diizinkan
        foreach ($roles as $role) {
            if ($user->role->value === $role) {
                return $next($request);
            }
        }

        return redirect()->route('home')->with('error', 'Anda tidak memiliki hak akses untuk halaman tersebut.');
    }
}
