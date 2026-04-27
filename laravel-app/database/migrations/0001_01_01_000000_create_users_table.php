<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Boleh kosong, karena identitas utama adalah wallet
            $table->string('wallet_address')->unique(); // Ini adalah "Username/Email" versi Web3
            $table->string('role')->default('muzakki');
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabel password_reset_tokens DIHAPUS karena tidak diperlukan

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};