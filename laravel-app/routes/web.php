<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ZakatController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\PenyaluranController;
use App\Http\Controllers\Admin\PemerintahController;

/*
|--------------------------------------------------------------------------
| 1. RUTE PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'index'])->name('login');

Route::get('/bayar-zakat', function () {
    if (Auth::check() && Auth::user()->role !== 'muzakki') {
        return redirect()->route(Auth::user()->role . '.dashboard');
    }
    return view('dashboard.muzakki.index');
})->name('zakat.form');

Route::post('/login-wallet', [AuthController::class, 'loginWallet'])->name('login.wallet');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| 2. RUTE TERPROTEKSI (Wajib Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /**
     * ROLE: MUZAKKI
     */
    Route::middleware(['role:muzakki'])->prefix('muzakki')->name('muzakki.')->group(function () {
        // Halaman Riwayat Setoran
        Route::get('/dashboard', [ZakatController::class, 'history'])->name('dashboard');
        
        // ✅ RUTE BARU: Halaman Pelacakan Transparansi (FIFO & Foto)
        Route::get('/tracking', [ZakatController::class, 'tracking'])->name('tracking'); 
        
        Route::post('/transaction/store', [ZakatController::class, 'store'])->name('transaction.store');
    });

    /**
     * ROLE: KEUANGAN
     */
    Route::middleware(['role:keuangan'])->prefix('keuangan')->name('keuangan.')->group(function () {
        Route::get('/dashboard', [KeuanganController::class, 'index'])->name('dashboard');
        Route::get('/pengajuan', [KeuanganController::class, 'pengajuan'])->name('pengajuan');
        Route::get('/pengajuan/{id}/review', [KeuanganController::class, 'review'])->name('review');
        Route::post('/pengajuan/{id}/approve', [KeuanganController::class, 'approve'])->name('approve');
        Route::get('/laporan', function () { return "Halaman Laporan (WIP)"; })->name('report');
        Route::get('/fifo', [KeuanganController::class, 'fifoLaporan'])->name('fifo');
    });

    /**
     * ROLE: PENYALURAN
     */
    Route::middleware(['role:penyaluran'])->prefix('penyaluran')->name('penyaluran.')->group(function () {
        Route::get('/dashboard', [PenyaluranController::class, 'index'])->name('dashboard');
        Route::post('/program/store', [PenyaluranController::class, 'store'])->name('store');
        Route::get('/program/konfirmasi', [PenyaluranController::class, 'konfirmasi'])->name('konfirmasi');
        Route::get('/program/{id}/upload-bukti', [PenyaluranController::class, 'showKonfirmasi'])->name('upload.bukti');
        Route::post('/program/{id}/upload-bukti', [PenyaluranController::class, 'uploadBukti'])->name('upload.bukti.store');
        Route::get('/program/{id}/fifo', [PenyaluranController::class, 'fifoProgram'])->name('fifo.program');
    });

      /**
     * ROLE: PEMERINTAH
     */
    Route::middleware(['role:pemerintah'])->prefix('pemerintah')->name('pemerintah.')->group(function () {
        // Rute default /dashboard diarahkan langsung ke halaman Pengumpulan ZIS
        Route::get('/dashboard', [PemerintahController::class, 'pengumpulanZisDskl'])->name('dashboard');
        
        // Rute spesifik untuk sidebar
        Route::get('/pengumpulan-zis-dskl', [PemerintahController::class, 'pengumpulanZisDskl'])->name('pengumpulan_zis_dskl');
        
        // Placeholder rute untuk Penyaluran & Laporan (Arahkan ke fungsi yang sama dulu, ganti controllernya nanti jika sudah dibuat)
        Route::get('/penyaluran-zis-dskl', [PemerintahController::class, 'pengumpulanZisDskl'])->name('penyaluran_zis_dskl'); 
        Route::get('/laporan-baznas', [PemerintahController::class, 'pengumpulanZisDskl'])->name('laporan_baznas'); 
    });
});