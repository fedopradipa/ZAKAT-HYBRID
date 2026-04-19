<?php
// app/Http/Controllers/Admin/KeuanganController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Distribution;
use App\Services\FifoService;
use App\Services\EthPriceService;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index(EthPriceService $ethPrice)
    {
        $recentTransactions = Transaction::with('user')->latest()->take(10)->get();
        $totalEth           = Transaction::sum('nominal');
        $totalTransaksi     = Transaction::count();

        // ✅ Status baru: belum_cair
        $pendingCount    = Distribution::where('status', 'belum_cair')->count();
        $pendingPrograms = Distribution::where('status', 'belum_cair')->latest()->take(3)->get();

        $ethToIdr = $ethPrice->getEthToIdr();
        $totalIdr = $totalEth * $ethToIdr;

        return view('dashboard.keuangan.index', compact(
            'recentTransactions',
            'totalEth',
            'totalTransaksi',
            'totalIdr',
            'ethToIdr',
            'pendingCount',
            'pendingPrograms'
        ));
    }

    public function pengajuan()
    {
        // ✅ Hanya tampilkan program yang belum cair
        $pendingPrograms = Distribution::where('status', 'belum_cair')
            ->latest()
            ->get();

        return view('dashboard.keuangan.pengajuan', compact('pendingPrograms'));
    }

    public function review($id)
    {
        $program = Distribution::with('mustahiks')->findOrFail($id);

        // ✅ Cek status baru
        if ($program->status !== 'belum_cair') {
            return redirect()->route('keuangan.pengajuan')
                ->with('error', 'Program ini sudah diproses sebelumnya.');
        }

        return view('dashboard.keuangan.review', compact('program'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate(['tx_hash' => 'required|string']);

        $program = Distribution::findOrFail($id);

        // ✅ Status baru setelah dicairkan: proses_pelaksanaan
        $program->update([
            'status'  => 'proses_pelaksanaan',
            'tx_hash' => $request->tx_hash,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Dana berhasil dicairkan dan status diperbarui.',
        ]);
    }

    public function fifoLaporan(FifoService $fifo)
    {
        $summary = $fifo->getSummary();
        return view('dashboard.keuangan.fifo', compact('summary'));
    }
}