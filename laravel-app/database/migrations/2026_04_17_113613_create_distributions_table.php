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

            $table->enum('status', [
                'belum_cair', 
                'proses_pelaksanaan', 
                'tidak_terlaksana', 
                'telah_terkonfirmasi'
            ])->default('belum_cair');

            // ── BUKTI KEBENARAN WEB3 ──
            $table->string('tx_hash')->nullable();
            
            // Hash file JSON yang mengunci Judul, Nominal, dan NIK/Nama Mustahik (Dienkripsi)
            $table->string('proposal_ipfs_hash')->nullable();
            
            // Hash foto-foto bukti penyerahan ke Mustahik
            $table->text('bukti_ipfs_hash')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};