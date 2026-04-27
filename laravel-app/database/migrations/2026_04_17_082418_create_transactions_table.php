<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // ⭐ MURNI WEB3: Tidak ada lagi user_id. 
            // Wallet address menjadi kunci utama relasi data!
            $table->string('wallet_address')->index(); 
            
            // Detail Dana
            $table->string('jenis_dana'); 
            $table->decimal('nominal', 20, 8); 
            $table->decimal('nominal_bersih', 20, 8)->default(0);
            $table->decimal('hak_amil', 20, 8)->default(0);
            
            // Integritas Blockchain
            $table->string('tx_hash')->unique();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            
            // Ekstra Data
            $table->json('metadata')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};