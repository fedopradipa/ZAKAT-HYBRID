<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Distribution;

class FifoService
{
    public function calculate(): array
    {
        // Hanya proses transaksi yang sudah dikonfirmasi oleh listener
        // is_verified = true berarti data 100% berasal dari blockchain event
        $deposits = Transaction::with('user')
            ->where('is_verified', true)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($tx) {
                return [
                    'transaction' => $tx,
                    'user'        => $tx->user,
                    'sisa'        => (float) $tx->nominal_bersih,
                ];
            })
            ->toArray();

        $pencairans = Distribution::where('status', 'telah_terkonfirmasi')
            ->orderBy('created_at', 'asc')
            ->get();

        $hasil   = [];
        $pointer = 0;

        foreach ($pencairans as $program) {
            $sisaProgram = (float) $program->dana_dibutuhkan;
            $allocations = [];

            while ($sisaProgram > 0 && $pointer < count($deposits)) {
                $deposit = &$deposits[$pointer];
                $ambil   = min($deposit['sisa'], $sisaProgram);

                $allocations[] = [
                    'transaction' => $deposit['transaction'],
                    'user'        => $deposit['user'],
                    'amount'      => $ambil,
                ];

                $deposit['sisa'] -= $ambil;
                $sisaProgram     -= $ambil;

                if ($deposit['sisa'] <= 0) {
                    $pointer++;
                }
            }

            $hasil[] = [
                'program'     => $program,
                'allocations' => $allocations,
                'terpenuhi'   => $sisaProgram <= 0,
            ];
        }

        return $hasil;
    }

    public function getSisaAntrian(): array
    {
        $allFifo      = $this->calculate();
        $totalDeposit = Transaction::with('user')
            ->where('is_verified', true)
            ->orderBy('created_at', 'asc')
            ->get();

        $terpakai = [];
        foreach ($allFifo as $entry) {
            foreach ($entry['allocations'] as $alloc) {
                $txId            = $alloc['transaction']->id;
                $terpakai[$txId] = ($terpakai[$txId] ?? 0) + $alloc['amount'];
            }
        }

        $antrian = [];
        foreach ($totalDeposit as $tx) {
            $sisa = (float) $tx->nominal_bersih - ($terpakai[$tx->id] ?? 0);
            if ($sisa > 0) {
                $antrian[] = [
                    'transaction'  => $tx,
                    'user'         => $tx->user,
                    'nominal_awal' => (float) $tx->nominal,
                    'terpakai'     => $terpakai[$tx->id] ?? 0,
                    'sisa'         => $sisa,
                ];
            }
        }

        return $antrian;
    }

    // ⭐ DIPERBAIKI: Menggunakan string $walletAddress untuk Web3
    public function calculateForUser(string $walletAddress): array
    {
        $allFifo   = $this->calculate();
        $hasilUser = [];
        $targetWallet = strtolower($walletAddress);

        foreach ($allFifo as $entry) {
            $allocUser = array_filter(
                $entry['allocations'],
                // Cocokkan berdasarkan wallet_address dari data transaksi
                fn($a) => strtolower($a['transaction']->wallet_address) === $targetWallet
            );

            if (!empty($allocUser)) {
                $hasilUser[] = [
                    'program'          => $entry['program'],
                    'allocations'      => array_values($allocUser),
                    'total_kontribusi' => array_sum(array_column($allocUser, 'amount')),
                ];
            }
        }

        return $hasilUser;
    }

    public function calculateForProgram(int $distributionId): array
    {
        $allFifo = $this->calculate();

        foreach ($allFifo as $entry) {
            if ($entry['program']->id === $distributionId) {
                return $entry;
            }
        }

        return [];
    }

    public function getSummary(): array
    {
        $allFifo      = $this->calculate();
        $totalDeposit = (float) Transaction::where('is_verified', true)->sum('nominal');
        $totalCair    = (float) Distribution::where('status', 'telah_terkonfirmasi')->sum('dana_dibutuhkan');
        $sisaAntrian  = $this->getSisaAntrian();

        return [
            'total_deposit'       => $totalDeposit,
            'total_cair'          => $totalCair,
            'sisa_antrian'        => array_sum(array_column($sisaAntrian, 'sisa')),
            'jumlah_program_cair' => count($allFifo),
            'detail_antrian'      => $sisaAntrian,
            'detail_alokasi'      => $allFifo,
        ];
    }
}