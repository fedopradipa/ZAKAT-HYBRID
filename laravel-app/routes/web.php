<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ZakatController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\PenyaluranController;
use App\Http\Controllers\Admin\PemerintahController;
use App\Http\Controllers\Public\ProgramController;

/*
|--------------------------------------------------------------------------
| 1. RUTE PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'index'])->name('login');


// ✅ RUTE BARU: Halaman Program Penyaluran (Untuk Publik)
Route::get('/program', [ProgramController::class, 'index'])->name('program.index');

// ✅ RUTE BARU: Halaman Detail Program (Penyebab error sebelumnya)
Route::get('/program/{id}', [ProgramController::class, 'show'])->name('program.show');


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
        // Dashboard redirect langsung ke pengajuan
        Route::get('/dashboard', function () {
            return redirect()->route('keuangan.pengajuan');
        })->name('dashboard');

        Route::get('/pengajuan', [KeuanganController::class, 'pengajuan'])->name('pengajuan');
        Route::get('/pengajuan/{id}/review', [KeuanganController::class, 'review'])->name('review');
        
        // ⭐ RUTE FULL WEB3 (DITAMBAHKAN DI SINI)
        Route::post('/pengajuan/{id}/prepare-web3', [KeuanganController::class, 'prepareWeb3'])->name('prepare_web3');
        Route::post('/pengajuan/{id}/approve', [KeuanganController::class, 'approve'])->name('approve');
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
        
        // ⭐ RUTE FULL WEB3 UNTUK BUKTI PENYALURAN
        Route::post('/program/{id}/prepare-bukti', [PenyaluranController::class, 'prepareBuktiWeb3'])->name('prepare_bukti');
        Route::post('/program/{id}/submit-konfirmasi', [PenyaluranController::class, 'submitKonfirmasi'])->name('submit_konfirmasi');

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
        Route::get('/penyaluran-zis-dskl', [PemerintahController::class, 'penyaluranZisDskl'])->name('penyaluran_zis_dskl');
        Route::get('/program-penyaluran', [PemerintahController::class, 'programPenyaluran'])->name('program_penyaluran');
        Route::get('/laporan-baznas', [PemerintahController::class, 'pengumpulanZisDskl'])->name('laporan_baznas'); 

    });
});