<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Pastikan tidak ada spasi/karakter apapun sebelum tag <?php di baris pertama.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Jika belum login, tendang ke halaman utama
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Sesi berakhir, silakan hubungkan dompet.');
        }

        // 2. Jika role tidak sesuai, arahkan ke dashboard yang benar
        if (Auth::user()->role !== $role) {
            $dest = match (Auth::user()->role) {
                'penyaluran' => 'penyaluran.dashboard',
                'keuangan'   => 'keuangan.dashboard',
                'pemerintah' => 'pemerintah.dashboard',
                default      => 'muzakki.dashboard',
            };
            return redirect()->route($dest)->with('error', 'Akses dibatasi.');
        }

        return $next($request);
    }
}
