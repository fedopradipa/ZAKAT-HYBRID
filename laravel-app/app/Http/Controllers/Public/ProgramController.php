<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Distribution;
use App\Services\EthPriceService;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    protected $ethPriceService;

    // Inject service konversi harga ETH
    public function __construct(EthPriceService $ethPriceService)
    {
        $this->ethPriceService = $ethPriceService;
    }

    /**
     * Menampilkan daftar program penyaluran.
     */
    public function index()
    {
        // Ambil rate ETH ke IDR saat ini
        $ethPriceIdr = $this->ethPriceService->getEthToIdr();

        $programs = Distribution::where('status', 'telah_terkonfirmasi')
                                ->latest()
                                ->get()
                                ->map(function ($program) use ($ethPriceIdr) {
                                    // IPFS Decode
                                    $hashes = json_decode($program->ipfs_hash, true) ?? [];
                                    $program->foto_urls = array_map(function ($hash) {
                                        return "https://gateway.pinata.cloud/ipfs/" . $hash;
                                    }, $hashes);
                                    $program->thumbnail = !empty($program->foto_urls) ? $program->foto_urls[0] : null;

                                    // Konversi Nominal ke Rupiah
                                    $program->dana_idr = floatval($program->dana_dibutuhkan) * $ethPriceIdr;

                                    return $program;
                                }); 
        
        return view('program.index', compact('programs'));
    }

    public function show($id)
{
    // Tambahkan ->with('mustahiks') agar nama penerima ikut terload
    $program = Distribution::where('status', 'telah_terkonfirmasi')
                ->with('mustahiks') // ← tambahkan ini
                ->findOrFail($id);

    $ethPriceIdr = $this->ethPriceService->getEthToIdr();
    $program->dana_idr = floatval($program->dana_dibutuhkan) * $ethPriceIdr;

    $hashes = json_decode($program->ipfs_hash, true) ?? [];
    $program->foto_urls = array_map(function ($hash) {
        return "https://gateway.pinata.cloud/ipfs/" . $hash;
    }, $hashes);

    return view('program.show', compact('program'));
}
}