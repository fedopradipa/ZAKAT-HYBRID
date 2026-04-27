<?php
// app/Http/Controllers/ZakatController.php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ZakatController extends Controller
{
    public function history(FifoService $fifo)
    {
        $wallet = strtolower(Auth::user()->wallet_address);
        
        $transactions = Transaction::where('wallet_address', $wallet)->latest()->get();
        $fifoAlokasi  = $fifo->calculateForUser($wallet);
        
        $totalSetor     = (float) $transactions->sum('nominal');
        $totalDialokasi = array_sum(array_column($fifoAlokasi, 'total_kontribusi'));
        $sisaBelumCair  = $totalSetor - $totalDialokasi;

        return view('dashboard.muzakki.history', compact(
            'transactions', 'fifoAlokasi', 'totalSetor', 'totalDialokasi', 'sisaBelumCair'
        ));
    }

    public function tracking(FifoService $fifo)
    {
        $wallet = strtolower(Auth::user()->wallet_address);
        
        $transactions = Transaction::where('wallet_address', $wallet)->get();
        $fifoAlokasi  = $fifo->calculateForUser($wallet);
        
        $totalSetor     = (float) $transactions->sum('nominal');
        $totalDialokasi = array_sum(array_column($fifoAlokasi, 'total_kontribusi'));
        $sisaBelumCair  = $totalSetor - $totalDialokasi;

        return view('dashboard.muzakki.tracking', compact(
            'fifoAlokasi', 'totalSetor', 'totalDialokasi', 'sisaBelumCair'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_dana'     => 'required|string',
            'nominal'        => 'required|numeric',
            'tx_hash'        => 'required|string',
            'wallet_address' => 'required|string',
            'nama'           => 'nullable|string',
            'email'          => 'nullable|email',
            'is_anonim'      => 'required|boolean',
        ]);

        $namaFix = $request->is_anonim ? 'Hamba Allah' : ($request->nama ?? 'Hamba Allah');
        $metadata = [
            'nama'      => $namaFix,
            'email'     => $request->email ?? null,
            'is_anonim' => $request->is_anonim,
            'source'    => 'frontend_tagging'
        ];

        // ⭐ SOLUSI MUTLAK: Jangan pakai updateOrCreate! Gunakan firstOrNew
        $tx = Transaction::firstOrNew(['tx_hash' => $request->tx_hash]);

        $tx->wallet_address = strtolower($request->wallet_address);
        $tx->jenis_dana     = $request->jenis_dana;
        $tx->nominal        = $request->nominal;

        // JIKA INI DATA BARU (Belum ada di DB), set ke FALSE.
        // Jika sudah ada (berarti webhook lebih dulu masuk), JANGAN SENTUH is_verified!
        if (!$tx->exists) {
            $tx->is_verified = false;
        }

        // Amankan Metadata: Gabungkan tanpa menghapus
        $existingMeta = is_array($tx->metadata) ? $tx->metadata : [];
        $tx->metadata = array_merge($existingMeta, $metadata);
        
        // Simpan
        $tx->save();

        Log::info('[ZAKAT] Metadata profil disimpan dengan aman.', ['tx_hash' => $request->tx_hash]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Metadata diterima. Menunggu verifikasi jaringan.',
        ]);
    }

    public function checkStatus($txHash)
    {
        $exists = Transaction::where('tx_hash', $txHash)->where('is_verified', true)->exists();
        return response()->json(['exists' => $exists]);
    }
}