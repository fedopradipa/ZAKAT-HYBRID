<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi');
            $table->date('tanggal_pelaksanaan');

            // Presisi untuk aset kripto (18 digit)
            $table->decimal('dana_dibutuhkan', 18, 8);

            $table->string('bidang');
            $table->string('sumber_dana');
            $table->string('asnaf');
            $table->string('bentuk_bantuan');
            $table->text('deskripsi_mustahik');
            $table->string('tipe_mustahik')->default('umum');

            /**
             * Alur Status Program (5 Tahap):
             *
             * 1. belum_cair          → Program dibuat tim penyaluran, menunggu pencairan keuangan
             * 2. proses_pelaksanaan  → Dana dicairkan keuangan ke dompet penyaluran
             * 3. tidak_terlaksana    → [AUTO CRON] Lewat tanggal & dana belum cair → dana dikembalikan ke kontrak
             * 4. belum_dikonfirmasi  → [AUTO CRON] Lewat tanggal & dana sudah cair → menunggu bukti foto
             * 5. telah_terkonfirmasi → Tim penyaluran upload foto bukti pelaksanaan ke IPFS
             */
            $table->string('status')->default('belum_cair');

            // Bukti transaksi blockchain saat pencairan dana
            $table->string('tx_hash')->nullable();

            // Hash foto bukti pelaksanaan di IPFS (JSON array untuk multiple foto)
            $table->text('ipfs_hash')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};