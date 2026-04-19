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
    protected $description = 'Cek tanggal pelaksanaan program dan update status secara otomatis (Cron Job)';

    public function handle()
    {
        $this->info('Memulai pengecekan status program...');

        $today        = Carbon::today();
        $updatedCount = 0;

        try {
            DB::beginTransaction();

            // -----------------------------------------------------------------
            // KONDISI 1: belum_cair + lewat tanggal → tidak_terlaksana
            // Dana belum dicairkan sama sekali, program hangus
            // TODO: Tambahkan Web3 REFUND ke brankas kontrak di sini
            // -----------------------------------------------------------------
            $expiredBelumCair = Distribution::where('status', 'belum_cair')
                ->whereDate('tanggal_pelaksanaan', '<', $today)
                ->get();

            foreach ($expiredBelumCair as $program) {
                $program->update(['status' => 'tidak_terlaksana']);
                $this->warn("Program ID #{$program->id} ('{$program->judul}') → TIDAK TERLAKSANA.");
                $updatedCount++;
            }

            // -----------------------------------------------------------------
            // KONDISI 2: proses_pelaksanaan + lewat tanggal → belum_dikonfirmasi
            // Dana sudah cair ke dompet penyaluran, tapi belum ada bukti foto
            // -----------------------------------------------------------------
            $expiredProses = Distribution::where('status', 'proses_pelaksanaan')
                ->whereDate('tanggal_pelaksanaan', '<', $today)
                ->get();

            foreach ($expiredProses as $program) {
                $program->update(['status' => 'belum_dikonfirmasi']);
                $this->info("Program ID #{$program->id} ('{$program->judul}') → BELUM DIKONFIRMASI.");
                $updatedCount++;
            }

            DB::commit();

            if ($updatedCount > 0) {
                $this->info("Selesai! Berhasil mengupdate {$updatedCount} program.");
            } else {
                $this->line("Tidak ada program yang melewati batas waktu hari ini.");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Terjadi kesalahan: " . $e->getMessage());
        }
    }
}