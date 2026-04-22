<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Distribution;
use App\Models\Mustahik;
use Illuminate\Http\Request;

class PemerintahController extends Controller
{
    public function index()
    {
        // 1. Hitung Total Pengumpulan Zakat
        $totalPengumpulan = Transaction::sum('nominal');

        // 2. Hitung Total yang sudah disalurkan (Selesai/Telah Terkonfirmasi)
        $totalDisalurkan = Distribution::where('status', 'telah_terkonfirmasi')->sum('dana_dibutuhkan');

        // 3. Hitung Efektivitas Penyaluran
        $efektivitas = $totalPengumpulan > 0 
            ? ($totalDisalurkan / $totalPengumpulan) * 100 
            : 0;

        // 4. Hitung Jumlah Penerima Manfaat
        $penerimaManfaat = Mustahik::count();

        // 5. Ambil Log Program yang sudah selesai untuk diaudit
        $logAudit = Distribution::where('status', 'telah_terkonfirmasi')
            ->orderBy('updated_at', 'desc')
            ->take(5) // Ambil 5 terbaru
            ->get();

        // Kirim data ke tampilan Dasbor Pemerintah
        return view('dashboard.pemerintah.index', compact(
            'totalPengumpulan',
            'efektivitas',
            'penerimaManfaat',
            'logAudit'
        ));
    }
}