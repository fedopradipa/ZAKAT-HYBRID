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
    /**
     * HALAMAN 1: Khusus menampilkan riwayat setoran saja
     */
    public function history(FifoService $fifo)
    {
        $transactions = Transaction::where('user_id', Auth::id())->latest()->get();
        
        // Data ringkasan (Summary) tetap butuh FIFO
        $fifoAlokasi     = $fifo->calculateForUser(Auth::id());
        $totalSetor      = (float) $transactions->sum('nominal');
        $totalDialokasi  = array_sum(array_column($fifoAlokasi, 'total_kontribusi'));
        $sisaBelumCair   = $totalSetor - $totalDialokasi;

        return view('dashboard.muzakki.history', compact(
            'transactions', 'fifoAlokasi', 'totalSetor', 'totalDialokasi', 'sisaBelumCair'
        ));
    }

    /**
     * HALAMAN 2: Khusus menampilkan pelacakan FIFO & Bukti IPFS
     */
    public function tracking(FifoService $fifo)
    {
        $transactions = Transaction::where('user_id', Auth::id())->get();
        
        $fifoAlokasi     = $fifo->calculateForUser(Auth::id());
        $totalSetor      = (float) $transactions->sum('nominal');
        $totalDialokasi  = array_sum(array_column($fifoAlokasi, 'total_kontribusi'));
        $sisaBelumCair   = $totalSetor - $totalDialokasi;

        return view('dashboard.muzakki.tracking', compact(
            'fifoAlokasi', 'totalSetor', 'totalDialokasi', 'sisaBelumCair'
        ));
    }

    /**
     * Menyimpan transaksi pembayaran zakat (dengan JSON metadata)
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_dana' => 'required|string',
            'nominal'    => 'required|numeric',
            'tx_hash'    => 'required|string|unique:transactions,tx_hash',
            'nama'       => 'nullable|string',
            'email'      => 'nullable|email',
            'no_hp'      => 'nullable|string',
            'is_anonim'  => 'required|boolean'
        ]);

        try {
            $metadataPembayar = [
                'nama'  => $request->is_anonim ? 'Hamba Allah' : ($request->nama ?? 'Hamba Allah'),
                'email' => $request->email ?? null,
                'no_hp' => $request->no_hp ?? null,
            ];

            Transaction::create([
                'user_id'    => Auth::id(),
                'jenis_dana' => $request->jenis_dana,
                'nominal'    => $request->nominal,
                'tx_hash'    => $request->tx_hash,
                'metadata'   => $metadataPembayar 
            ]);

            return response()->json(['status' => 'success', 'message' => 'Data Zakat Berhasil Disimpan']);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan transaksi: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }
}