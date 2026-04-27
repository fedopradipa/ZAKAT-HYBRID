<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $role   = $request->role; 

        try {
            // Cukup daftarkan / ambil data User berdasarkan Wallet
            $user = User::firstOrCreate(
                ['wallet_address' => $wallet],
                [
                    'name' => 'Hamba Allah',
                    'role' => $role,
                ]
            );

            if ($user->role !== $role) {
                $user->update(['role' => $role]);
            }

            // (Kode Auto-Claim user_id TELAH DIHAPUS karena kita murni pakai wallet_address)

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
            // Tangkap dan catat error aslinya di file log jika masih ada masalah
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