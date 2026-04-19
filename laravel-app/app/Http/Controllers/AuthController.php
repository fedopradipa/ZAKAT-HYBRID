<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            if ($role !== 'muzakki') {
                return redirect()->to($this->getRedirectUrl($role));
            }
        }
        return view('auth.login');
    }

    public function loginWallet(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string',
            'role'           => 'required|in:muzakki,keuangan,pemerintah,penyaluran',
            'previous_url'   => 'nullable|string',
        ]);

        $wallet = strtolower($request->wallet_address);
        $role   = $request->role; // Sudah dideteksi on-chain di frontend

        try {
            // ✅ firstOrCreate: buat user baru jika belum ada
            // TIDAK overwrite role jika user sudah ada di DB
            $user = User::firstOrCreate(
                ['wallet_address' => $wallet],
                [
                    'name'     => 'Hamba Allah',
                    'email'    => $wallet . '@zakat.local',
                    'password' => Hash::make('web3_secret_static_pass'),
                    'role'     => $role,
                ]
            );

            // ✅ Jika user sudah ada tapi rolenya beda (misal data lama salah),
            // update role sesuai yang dari blockchain
            if ($user->role !== $role) {
                $user->update(['role' => $role]);
            }

            Auth::login($user);
            $request->session()->regenerate();

            $redirectUrl = ($user->role !== 'muzakki')
                ? $this->getRedirectUrl($user->role)
                : ($request->previous_url ?? url('/'));

            return response()->json([
                'status'       => 'success',
                'message'      => 'Otentikasi Web3 Berhasil',
                'role'         => $user->role,
                'redirect_url' => $redirectUrl,
            ]);

        } catch (\Exception $e) {
            Log::error('Web3 Login Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal sinkronisasi wallet.',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Dompet terputus.');
    }

    private function getRedirectUrl($role): string
    {
        return match ($role) {
            'keuangan'   => route('keuangan.dashboard'),
            'pemerintah' => route('pemerintah.dashboard'),
            'penyaluran' => route('penyaluran.dashboard'),
            'muzakki'    => route('muzakki.dashboard'),
            default      => url('/'),
        };
    }
}