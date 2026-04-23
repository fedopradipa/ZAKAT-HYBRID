<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('nominal_bersih', 20, 8)->default(0)->after('nominal');
            $table->decimal('hak_amil', 20, 8)->default(0)->after('nominal_bersih');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['nominal_bersih', 'hak_amil']);
        });
    }
};