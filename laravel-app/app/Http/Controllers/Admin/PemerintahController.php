<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Distribution;
use App\Models\Mustahik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\EthPriceService; // Tambahkan import service

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
     * Inject EthPriceService ke dalam parameter fungsi
     */
    public function pengumpulanZisDskl(Request $request, EthPriceService $ethPriceService)
    {
        $tahun = $request->input('tahun', now()->year);
        
        // Ambil harga ETH to IDR dari CoinGecko (Service)
        $ethPriceIdr = $ethPriceService->getEthToIdr();

        // ── CARDS ──────────────────────────────────────────────────────
        $totalsRaw = Transaction::whereYear('created_at', $tahun)
            ->select('jenis_dana', DB::raw('SUM(nominal) as total'))
            ->groupBy('jenis_dana')
            ->pluck('total', 'jenis_dana');

        $totalZakat         = 0;
        $totalInfakTerikat  = 0;
        $totalInfakBebas    = 0;
        $totalHakAmil       = 0;

        foreach ($totalsRaw as $jenis => $total) {
            $jenisLower = strtolower($jenis);

            if (str_contains($jenisLower, 'zakat')) {
                $totalZakat += $total;
            } elseif (str_contains($jenisLower, 'terikat')) {
                $totalInfakTerikat += $total;
            } elseif (str_contains($jenisLower, 'amil')) {
                $totalHakAmil += $total;
            } else {
                $totalInfakBebas += $total;
            }
        }

        // ── DATA CHART BULANAN ─────────────────────────────────────────
        $chartRaw = Transaction::whereYear('created_at', $tahun)
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                'jenis_dana',
                DB::raw('SUM(nominal) as total')
            )
            ->groupBy('bulan', 'jenis_dana')
            ->get();

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
                $dataInfakBulan[$idx] += $row->total;
            }
        }

        // ── DONUT CHART ────────────────────────────────────────────────
        $totalKeseluruhan   = $totalZakat + $totalInfakTerikat + $totalInfakBebas + $totalHakAmil;
        $donutData = [
            round($totalZakat,        4),
            round($totalInfakTerikat, 4),
            round($totalInfakBebas,   4),
            round($totalHakAmil,      4),
        ];

        // ── TABEL ──────────────────────────────────────────────────────
        $dataMuzakki = Transaction::with('user')
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.pemerintah.pengumpulanZIS.index', compact(
            'tahun',
            'ethPriceIdr', // Kirim ke View
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