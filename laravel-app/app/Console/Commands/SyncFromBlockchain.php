<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncFromBlockchain extends Command
{
    protected $signature   = 'zakat:sync-blockchain
                              {--from=0 : Block number awal}
                              {--dry-run : Tampilkan saja tanpa simpan}';
    protected $description = 'Rebuild tabel transactions dari event log Blockchain';

    private string $rpcUrl          = 'http://127.0.0.1:8545';
    private string $contractAddress = '0x5FbDB2315678afecb367f032d93F642f64180aa3';

    public function handle(): void
    {
        $fromBlock = $this->option('from') ?? '0x0';
        $isDryRun  = $this->option('dry-run');

        $this->info('Mengambil event DepositZIS dari Blockchain...');
        $this->info('From block: ' . $fromBlock);
        $isDryRun && $this->warn('DRY RUN MODE — tidak ada data yang disimpan');
        $this->newLine();

        // ── Ambil semua log event DepositZIS dari chain ───────────
        // Topic0 = keccak256("DepositZIS(address,uint256,uint256,uint256,string,uint256)")
        $response = Http::post($this->rpcUrl, [
            'jsonrpc' => '2.0',
            'method'  => 'eth_getLogs',
            'params'  => [[
                'fromBlock' => '0x0',
                'toBlock'   => 'latest',
                'address'   => $this->contractAddress,
            ]],
            'id' => 1,
        ]);

        $logs = $response->json('result');

        if (empty($logs)) {
            $this->info('Tidak ada event ditemukan di blockchain.');
            return;
        }

        $this->info('Total event ditemukan: ' . count($logs));
        $this->newLine();

        $bar     = $this->output->createProgressBar(count($logs));
        $sukses  = 0;
        $skip    = 0;
        $gagal   = 0;

        foreach ($logs as $log) {
            $txHash = $log['transactionHash'];

            // Ambil detail transaksi untuk dapat nilai ETH
            $txDetail = Http::post($this->rpcUrl, [
                'jsonrpc' => '2.0',
                'method'  => 'eth_getTransactionByHash',
                'params'  => [$txHash],
                'id'      => 1,
            ])->json('result');

            if (empty($txDetail)) {
                $gagal++;
                $bar->advance();
                continue;
            }

            // Skip jika sudah ada di DB
            if (Transaction::where('tx_hash', $txHash)->exists()) {
                $skip++;
                $bar->advance();
                continue;
            }

            // Ambil wallet dari transaksi
            $walletAddress = strtolower($txDetail['from']);
            $user          = User::whereRaw('LOWER(wallet_address) = ?', [$walletAddress])->first();
            $nominal       = hexdec($txDetail['value']) / 1e18;
            $hakAmil       = $nominal * 0.125;
            $nominalBersih = $nominal - $hakAmil;

            if ($isDryRun) {
                $this->line("  TX: {$txHash} | Wallet: {$walletAddress} | Nominal: {$nominal} ETH");
                $sukses++;
                $bar->advance();
                continue;
            }

            try {
                Transaction::create([
                    'user_id'        => $user?->id,
                    'jenis_dana'     => 'Sync dari Blockchain',
                    'nominal'        => $nominal,
                    'nominal_bersih' => $nominalBersih,
                    'hak_amil'       => $hakAmil,
                    'tx_hash'        => $txHash,
                    'is_verified'    => true,
                    'verified_at'    => now(),
                    'metadata'       => [
                        'nama'   => $user->name ?? 'Hamba Allah',
                        'email'  => null,
                        'no_hp'  => null,
                        'source' => 'blockchain_sync',
                    ],
                ]);
                $sukses++;
            } catch (\Exception $e) {
                $gagal++;
                Log::error('[SYNC] Gagal: ' . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Selesai! Sukses: {$sukses} | Skip: {$skip} | Gagal: {$gagal}");
    }
}