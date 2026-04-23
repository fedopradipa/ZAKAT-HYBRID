<?php
// app/Http/Controllers/Admin/KeuanganController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distribution;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    // index() → DIHAPUS (digantikan redirect di routes)
    // fifoLaporan() → DIHAPUS

    public function pengajuan()
    {
        $pendingPrograms = Distribution::where('status', 'belum_cair')
            ->latest()
            ->get();

        return view('dashboard.keuangan.pengajuan', compact('pendingPrograms'));
    }

    public function review($id)
    {
        $program = Distribution::with('mustahiks')->findOrFail($id);

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

        $program->update([
            'status'  => 'proses_pelaksanaan',
            'tx_hash' => $request->tx_hash,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Dana berhasil dicairkan dan status diperbarui.',
        ]);
    }
}