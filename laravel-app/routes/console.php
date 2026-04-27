<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Tempat Anda mendefinisikan jadwal eksekusi otomatis (Cron Jobs).
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// --- Jadwal Update Status Penyaluran (Bawaan Lama) ---
Schedule::command('penyaluran:update-status')
    ->dailyAt('00:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// --- Jadwal "Sapu Bersih" (Rebuild & Recovery) menggunakan sync.js (Baru) ---
Schedule::call(function () {
    // Tentukan lokasi file sync.js Anda
    // Karena laravel-app dan smart-contract sejajar, kita mundur 1 folder
    $smartContractPath = base_path('../smart-contract');

    Log::info('[SCHEDULER] Menjalankan Bot Sapu Bersih (sync.js)...');

    // Eksekusi Node.js
    $result = Process::path($smartContractPath)->run('node sync.js');

    if ($result->successful()) {
        Log::info('[SCHEDULER] Sapu Bersih Selesai. Output: ' . $result->output());
    } else {
        Log::error('[SCHEDULER] Gagal menjalankan sync.js. Error: ' . $result->errorOutput());
    }
})
// UNTUK TESTING LOKAL: Jalankan setiap menit
->everyMinute() 

// JIKA SUDAH LIVE (PRODUKSI), GANTI MENJADI:
// ->dailyAt('01:00')->timezone('Asia/Jakarta')

->description('Menjalankan proses rekonsiliasi data Zakat dari Blockchain (Sapu Bersih)');