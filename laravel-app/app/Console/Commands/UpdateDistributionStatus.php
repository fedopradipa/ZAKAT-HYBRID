<?php
// app/Console/Commands/UpdateDistributionStatus.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Distribution;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateDistributionStatus extends Command
{
    protected $signature   = 'penyaluran:update-status';
    protected $description = 'Cek program yang kadaluarsa (belum cair & lewat tanggal) dan update ke MySQL';

    public function handle()
    {
        $this->info('Memulai pengecekan program kadaluarsa...');

        // ⭐ KUNCI MASALAH: Set Zona Waktu ke WIB!
        // Jika tidak, jam 00:00 - 06:59 pagi Laravel masih mengira hari kemarin (UTC)
        $today = Carbon::now('Asia/Jakarta')->startOfDay();
        
        $updatedCount = 0;

        try {
            DB::beginTransaction();

            // KONDISI: belum_cair + tanggal pelaksanaan sudah lewat dari $today
            $expiredBelumCair = Distribution::where('status', 'belum_cair')
                ->whereDate('tanggal_pelaksanaan', '<', $today)
                ->get();

            foreach ($expiredBelumCair as $program) {
                // UPDATE PERMANEN KE MYSQL
                $program->update(['status' => 'tidak_terlaksana']);
                $this->warn("Program ID #{$program->id} ('{$program->judul}') → KADALUARSA / BATAL.");
                $updatedCount++;
            }

            DB::commit();

            if ($updatedCount > 0) {
                $this->info("Selesai! Berhasil membatalkan {$updatedCount} program secara permanen di MySQL.");
            } else {
                $this->line("Tidak ada program yang kadaluarsa hari ini.");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Terjadi kesalahan: " . $e->getMessage());
        }
    }
}