<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin Penyaluran
        User::create([
            'name'           => 'Bidang Penyaluran',
            // Diambil dari terminal Anda, diubah ke huruf kecil (lowercase)
            'wallet_address' => strtolower('0x70997970C51812dc3A010C7d01b50e0d17dc79C8'),
            'role'           => 'penyaluran',
        ]);

        // 2. Akun Admin Keuangan
        User::create([
            'name'           => 'Bidang Keuangan',
            'wallet_address' => strtolower('0x3C44CdDdB6a900fa2b585dd299e03d12FA4293BC'),
            'role'           => 'keuangan',
        ]);

        // 3. Akun Pemerintah / Pengawas
        User::create([
            'name'           => 'Pemerintah (Kemenag)',
            'wallet_address' => strtolower('0x90F79bf6EB2c4f870365E785982E1f101E93b906'),
            'role'           => 'pemerintah',
        ]);

        // 4. Akun Contoh Muzakki (Opsional, tapi bagus untuk testing)
        User::create([
            'name'           => 'Fedo (Muzakki Test)',
            'wallet_address' => strtolower('0x9965507D1a55bcC2695C58ba16FB37d819B0A4dc'),
            'role'           => 'muzakki',
        ]);
    }
}