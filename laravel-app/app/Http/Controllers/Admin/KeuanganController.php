<?php
// app/Http/Controllers/Admin/KeuanganController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distribution;
use App\Services\PinataService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt; 

class KeuanganController extends Controller
{
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

    // ====================================================================
    // ⭐ FULL WEB3: 1. Persiapkan IPFS sebelum MetaMask dipanggil (AJAX)
    // ====================================================================
    public function prepareWeb3($id, PinataService $pinata)
    {
        $program = Distribution::with('mustahiks')->findOrFail($id);

        // 1. Enkripsi Data Sensitif Mustahik (Sesuai UU PDP)
        $mustahikDetail = $program->mustahiks->map(function ($m) {
            return [
                'nik'    => $m->nik,
                'nama'   => $m->nama,
                'alamat' => $m->alamat,
                'bantuan'=> $m->bentuk_bantuan
            ];
        })->toArray();

        // 2. Gabungkan Data (Public Transparan + Private Terenkripsi)
        $payload = [
            'public_info' => [
                'id_program'          => $program->id,
                'judul'               => $program->judul,
                'deskripsi'           => $program->deskripsi,
                'dana_eth'            => (float) $program->dana_dibutuhkan,
                'tanggal_pelaksanaan' => $program->tanggal_pelaksanaan->format('Y-m-d'),
                'bidang'              => $program->bidang,
                'asnaf'               => $program->asnaf,
                // ⭐ DATA TAMBAHAN YANG SEBELUMNYA TERLEWAT
                'sumber_dana'         => $program->sumber_dana,
                'bentuk_bantuan'      => $program->bentuk_bantuan,
                'deskripsi_mustahik'  => $program->deskripsi_mustahik,
                'tipe_mustahik'       => $program->tipe_mustahik,
            ],
            // Data array di-encode lalu dienkripsi AES-256
            'encrypted_detail' => Crypt::encryptString(json_encode($mustahikDetail))
        ];

        try {
            // 3. Upload ke IPFS menggunakan layanan Pinata Anda
            $result = $pinata->uploadJson($payload, "Proposal_ID_{$program->id}");
            
            // 4. Kembalikan IPFS Hash (Resi) ke Frontend untuk MetaMask
            return response()->json([
                'status'    => 'success',
                'ipfs_hash' => $result['ipfs_hash'], 
                'nominal'   => $program->dana_dibutuhkan,
                'program_id'=> $program->id
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ====================================================================
    // ⭐ FULL WEB3: 2. Simpan Transaksi setelah MetaMask Sukses (AJAX)
    // ====================================================================
    public function approve(Request $request, $id)
    {
        $request->validate([
            'tx_hash'            => 'required|string',
            'proposal_ipfs_hash' => 'required|string', 
        ]);

        $program = Distribution::findOrFail($id);

        $program->update([
            'status'             => 'proses_pelaksanaan',
            'tx_hash'            => $request->tx_hash,
            'proposal_ipfs_hash' => $request->proposal_ipfs_hash, 
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Dana berhasil dicairkan. Proposal terkunci di Blockchain.',
        ]);
    }
}