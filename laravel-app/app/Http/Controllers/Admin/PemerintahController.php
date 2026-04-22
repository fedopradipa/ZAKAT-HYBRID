<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Distribution;
use App\Models\Mustahik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemerintahController extends Controller
{
    public function index()
    {
        $totalPengumpulan = Transaction::sum('nominal');
        $totalDisalurkan  = Distribution::where('status', 'telah_terkonfirmasi')->sum('dana_dibutuhkan');
        $efektivitas      = $totalPengumpulan > 0 ? ($totalDisalurkan / $totalPengumpulan) * 100 : 0;
        $penerimaManfaat  = Mustahik::count();
        $logAudit         = Distribution::where('status', 'telah_terkonfirmasi')
                                ->orderBy('updated_at', 'desc')->take(5)->get();

        return view('dashboard.pemerintah.index', compact(
            'totalPengumpulan', 'efektivitas', 'penerimaManfaat', 'logAudit'
        ));
    }

    /**
     * Halaman Pengumpulan ZIS-DSKL
     */
    public function pengumpulanZisDskl(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        // ── CARDS ──────────────────────────────────────────────────────
        // Ambil total per jenis_dana untuk tahun yang dipilih
        $totalsRaw = Transaction::whereYear('created_at', $tahun)
            ->select('jenis_dana', DB::raw('SUM(nominal) as total'))
            ->groupBy('jenis_dana')
            ->pluck('total', 'jenis_dana');

        $totalZakat         = 0;
        $totalInfakTerikat  = 0;
        $totalInfakBebas    = 0;
        $totalHakAmil       = 0;

        // Petakan string dinamis dari DB ke 4 Kategori Card
        foreach ($totalsRaw as $jenis => $total) {
            $jenisLower = strtolower($jenis);

            if (str_contains($jenisLower, 'zakat')) {
                $totalZakat += $total;
            } elseif (str_contains($jenisLower, 'terikat')) {
                $totalInfakTerikat += $total;
            } elseif (str_contains($jenisLower, 'amil')) {
                $totalHakAmil += $total;
            } else {
                // Infak Umum, DSKL, Fidyah, dll masuk ke Infak Bebas (Tidak Terikat)
                $totalInfakBebas += $total;
            }
        }

        // ── DATA CHART BULANAN ─────────────────────────────────────────
        // Ambil total per bulan, dipisah per jenis_dana
        $chartRaw = Transaction::whereYear('created_at', $tahun)
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                'jenis_dana',
                DB::raw('SUM(nominal) as total')
            )
            ->groupBy('bulan', 'jenis_dana')
            ->get();

        // Inisialisasi array 12 bulan dengan 0
        $bulanLabel = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
        $dataZakatBulan      = array_fill(0, 12, 0);
        $dataInfakBulan      = array_fill(0, 12, 0);
        $dataTotalBulan      = array_fill(0, 12, 0);

        foreach ($chartRaw as $row) {
            $idx = $row->bulan - 1; // index 0-11
            $dataTotalBulan[$idx] += $row->total;

            $jenisLower = strtolower($row->jenis_dana);

            if (str_contains($jenisLower, 'zakat')) {
                $dataZakatBulan[$idx] += $row->total;
            } else {
                // Semua selain Zakat (Infak, DSKL, Fidyah) masuk ke line chart Infak
                $dataInfakBulan[$idx] += $row->total;
            }
        }

        // ── DONUT CHART ────────────────────────────────────────────────
        $totalKeseluruhan   = $totalZakat + $totalInfakTerikat + $totalInfakBebas + $totalHakAmil;
        $donutData = [
            round($totalZakat,        2),
            round($totalInfakTerikat, 2),
            round($totalInfakBebas,   2),
            round($totalHakAmil,      2),
        ];

        // ── TABEL ──────────────────────────────────────────────────────
        $dataMuzakki = Transaction::with('user')
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // agar filter tahun tetap saat ganti halaman

        return view('dashboard.pemerintah.pengumpulanZIS.index', compact(
            'tahun',
            'totalZakat',
            'totalInfakTerikat',
            'totalInfakBebas',
            'totalHakAmil',
            'totalKeseluruhan',
            'bulanLabel',
            'dataTotalBulan',
            'dataZakatBulan',
            'dataInfakBulan',
            'donutData',
            'dataMuzakki'
        ));
    }
}