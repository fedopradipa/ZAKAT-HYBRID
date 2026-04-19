<?php
// app/Http/Controllers/Admin/PenyaluranController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distribution;
use App\Models\Transaction;
use App\Models\Mustahik;
use App\Services\FifoService;
use App\Services\PinataService;
use App\Services\EthPriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyaluranController extends Controller
{
    public function index(EthPriceService $ethPriceService)
    {
        $totalTerkumpul  = Transaction::sum('nominal');

        // ✅ Status baru: proses_pelaksanaan, belum_dikonfirmasi, telah_terkonfirmasi
        $totalDisalurkan = Distribution::whereIn('status', [
            'proses_pelaksanaan',
            'belum_dikonfirmasi',
            'telah_terkonfirmasi',
        ])->sum('dana_dibutuhkan');

        $sisaSaldo           = $totalTerkumpul - $totalDisalurkan;
        $recentDistributions = Distribution::latest()->take(5)->get();
        $ethPrice            = $ethPriceService->getEthToIdr();

        return view('dashboard.penyaluran.index', compact(
            'totalTerkumpul',
            'totalDisalurkan',
            'sisaSaldo',
            'recentDistributions',
            'ethPrice'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'              => 'required|string|max:255',
            'deskripsi'          => 'required|string',
            'tanggal_pelaksanaan'=> 'required|date',
            'dana_dibutuhkan'    => 'required|numeric',
            'bidang'             => 'required|string',
            'sumber_dana'        => 'required|string',
            'asnaf'              => 'required|string',
            'bentuk_bantuan'     => 'required|string',
            'deskripsi_mustahik' => 'required|string',
            'tipe_mustahik'      => 'required|in:umum,detail',
            'mustahik.*.nik'     => 'required_if:tipe_mustahik,detail|string|max:20',
            'mustahik.*.nama'    => 'required_if:tipe_mustahik,detail|string|max:255',
            'mustahik.*.alamat'  => 'required_if:tipe_mustahik,detail|string',
            'mustahik.*.bantuan' => 'required_if:tipe_mustahik,detail|string',
        ]);

        try {
            DB::beginTransaction();

            $distribution = Distribution::create([
                'judul'              => $request->judul,
                'deskripsi'          => $request->deskripsi,
                'tanggal_pelaksanaan'=> $request->tanggal_pelaksanaan,
                'dana_dibutuhkan'    => $request->dana_dibutuhkan,
                'bidang'             => $request->bidang,
                'sumber_dana'        => $request->sumber_dana,
                'asnaf'              => $request->asnaf,
                'bentuk_bantuan'     => $request->bentuk_bantuan,
                'deskripsi_mustahik' => $request->deskripsi_mustahik,
                'tipe_mustahik'      => $request->tipe_mustahik,
                'status'             => 'belum_cair', // ✅ Status awal
            ]);

            if ($request->tipe_mustahik === 'detail' && $request->has('mustahik')) {
                foreach ($request->mustahik as $item) {
                    Mustahik::create([
                        'distribution_id' => $distribution->id,
                        'nik'             => $item['nik'],
                        'nama'            => $item['nama'],
                        'alamat'          => $item['alamat'],
                        'bentuk_bantuan'  => $item['bantuan'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('penyaluran.dashboard')
                ->with('success', 'Program berhasil diajukan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function konfirmasi()
    {
        $programs = Distribution::with('mustahiks')->latest()->get();
        return view('dashboard.penyaluran.konfirmasi', compact('programs'));
    }

    public function showKonfirmasi($id)
    {
        $program = Distribution::with('mustahiks')->findOrFail($id);

        // ✅ Hanya bisa upload jika sudah proses_pelaksanaan atau belum_dikonfirmasi
        if (!in_array($program->status, ['proses_pelaksanaan', 'belum_dikonfirmasi'])) {
            return redirect()->route('penyaluran.konfirmasi')
                ->with('error', 'Program tidak dapat dikonfirmasi saat ini.');
        }

        // ✅ Jika sudah dikonfirmasi
        if ($program->status === 'telah_terkonfirmasi') {
            return redirect()->route('penyaluran.konfirmasi')
                ->with('error', 'Program sudah dikonfirmasi sebelumnya.');
        }

        return view('dashboard.penyaluran.upload-bukti', compact('program'));
    }

    public function uploadBukti(Request $request, $id, PinataService $pinata)
    {
        $request->validate([
            'foto_bukti'   => 'required|array|min:1',
            'foto_bukti.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
            'catatan'      => 'nullable|string|max:500',
        ]);

        $program = Distribution::findOrFail($id);

        try {
            $hashes = [];

            foreach ($request->file('foto_bukti') as $file) {
                $result   = $pinata->uploadFile($file, $program->judul);
                $hashes[] = $result['ipfs_hash'];
            }

            // ✅ Update status ke telah_terkonfirmasi, hapus konfirmasi_status
            $program->update([
                'ipfs_hash' => json_encode($hashes),
                'status'    => 'telah_terkonfirmasi',
            ]);

            return redirect()->route('penyaluran.konfirmasi')
                ->with('success', count($hashes) . ' foto berhasil diupload ke IPFS!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload: ' . $e->getMessage());
        }
    }

    public function fifoProgram($id, FifoService $fifo)
    {
        $program = Distribution::findOrFail($id);

        // ✅ FIFO tersedia untuk semua status yang sudah cair
        if (!in_array($program->status, [
            'proses_pelaksanaan',
            'belum_dikonfirmasi',
            'telah_terkonfirmasi',
        ])) {
            return redirect()->route('penyaluran.konfirmasi')
                ->with('error', 'Dana belum dicairkan, data FIFO belum tersedia.');
        }

        $detail = $fifo->calculateForProgram($id);
        return view('dashboard.penyaluran.fifo-program', compact('program', 'detail'));
    }
}