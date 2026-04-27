<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\ZakatController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rute untuk Listener Real-time
Route::post('/webhook/verify-zakat', [WebhookController::class, 'verifyZakat'])->name('api.webhook.verify-zakat');

// Rute untuk Sync Massal (Sapu Bersih / Rebuild)
Route::post('/webhook/rebuild-zakat', [WebhookController::class, 'rebuildZakat'])->name('api.webhook.rebuild-zakat');

// ⭐ TAMBAHAN WAJIB WEB3: Rute untuk Rebuild Penyaluran (Jika ini tidak ada, sync.js akan Error 404)
Route::post('/webhook/rebuild-penyaluran', [WebhookController::class, 'rebuildPenyaluranWeb3'])->name('api.webhook.rebuild-penyaluran');

// Rute untuk Pengecekan Cerdas (Smart Polling) Frontend
Route::get('/zakat/check/{tx_hash}', [ZakatController::class, 'checkStatus']);