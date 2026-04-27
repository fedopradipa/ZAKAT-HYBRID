<?php
// app/Http/Controllers/Api/WebhookController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Distribution;
use App\Models\Mustahik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WebhookController extends Controller
{
    // ====================================================================
    // 1. ZAKAT: Verifikasi Real-Time
    // ====================================================================
    public function verifyZakat(Request $request)
    {
        $secret = $request->header('X-Webhook-Secret');
        if ($secret !== env('WEBHOOK_SECRET', 'rahasia-baznas-123')) {
            Log::warning('[WEBHOOK] Akses tidak sah dari IP: ' . $request->ip());
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $txHash        = $request->input('tx_hash');
        $walletAddress = strtolower($request->input('wallet_address'));
        $nominal       = (float) $request->input('nominal');
        $nominalBersih = (float) $request->input('nominal_bersih');
        $hakAmil       = (float) $request->input('hak_amil');
        $jenisDana     = $request->input('jenis_dana');

        $tx = Transaction::firstOrNew(['tx_hash' => $txHash]);

        if ($tx->exists && $tx->is_verified) {
            return response()->json(['status' => 'success', 'message' => 'Sudah terverifikasi sebelumnya']);
        }

        $tx->wallet_address = $walletAddress;
        $tx->jenis_dana     = $jenisDana;
        $tx->nominal        = $nominal;
        $tx->nominal_bersih = $nominalBersih;
        $tx->hak_amil       = $hakAmil;
        
        $tx->is_verified    = true;
        $tx->verified_at    = now();

        $existingMeta = is_array($tx->metadata) ? $tx->metadata : [];
        if (empty($existingMeta)) {
            $tx->metadata = ['source' => 'blockchain_event'];
        } else {
            $tx->metadata = array_merge($existingMeta, ['source' => 'blockchain_event_synced']);
        }

        $tx->save();

        Log::info("[WEBHOOK] Transaksi {$txHash} disahkan jadi TRUE.");
        return response()->json(['status' => 'success', 'message' => 'Transaksi berhasil terverifikasi']);
    }

    // ====================================================================
    // 2. ZAKAT: Sapu Bersih (Rebuild Massal)
    // ====================================================================
    public function rebuildZakat(Request $request)
    {
        $secret = $request->header('X-Webhook-Secret');
        if ($secret !== env('WEBHOOK_SECRET', 'rahasia-baznas-123')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $blockchainTxs = $request->input('transactions');

        if (empty($blockchainTxs)) {
            return response()->json(['message' => 'Tidak ada data dari Blockchain'], 400);
        }

        $validHashes = [];
        $manipulatedCount = 0;

        foreach ($blockchainTxs as $bTx) {
            $validHashes[] = $bTx['tx_hash'];
            $walletAddress = strtolower($bTx['wallet_address']);

            $existing = Transaction::where('tx_hash', $bTx['tx_hash'])->first();
            if ($existing && ((float)$existing->nominal !== (float)$bTx['nominal'] || $existing->jenis_dana !== $bTx['jenis_dana'])) {
                $manipulatedCount++;
            }

            Transaction::updateOrCreate(
                ['tx_hash' => $bTx['tx_hash']],
                [
                    'wallet_address' => $walletAddress,
                    'jenis_dana'     => $bTx['jenis_dana'],
                    'nominal'        => $bTx['nominal'],
                    'nominal_bersih' => $bTx['nominal_bersih'],
                    'hak_amil'       => $bTx['hak_amil'],
                    'is_verified'    => true,
                    'verified_at'    => $existing ? $existing->verified_at : now(),
                ]
            );
        }

        $deletedCount = Transaction::whereNotIn('tx_hash', $validHashes)->delete();

        $pesan = "Rebuild sukses! {$manipulatedCount} data manipulasi diperbaiki. {$deletedCount} data fiktif dihapus.";
        Log::info("[SYNC-REBUILD] {$pesan}");

        return response()->json([
            'status'  => 'success',
            'message' => $pesan
        ]);
    }

    // ====================================================================
    // 3. PENYALURAN: Ultimate Web3 Self-Healing (Super Bersih & Ketat)
    // ====================================================================
    public function rebuildPenyaluranWeb3(Request $request)
    {
        if ($request->header('X-Webhook-Secret') !== env('WEBHOOK_SECRET', 'rahasia-baznas-123')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $events = $request->input('events'); 
        $diperbaiki = 0;

        foreach ($events as $event) {
            $programId    = $event['program_id'];
            $proposalHash = $event['proposal_ipfs_hash']; 
            $buktiHash    = $event['bukti_ipfs_hash'] ?? null; 
            $txHash       = $event['tx_hash'];
            $statusBc     = $event['status']; 
            
            $statusSeharusnya = $statusBc == 2 ? 'telah_terkonfirmasi' : 'proses_pelaksanaan';

            try {
                DB::beginTransaction();

                $program = Distribution::find($programId);
                
                $ipfsUrl = "https://gateway.pinata.cloud/ipfs/" . $proposalHash;
                $response = Http::get($ipfsUrl);

                if ($response->successful()) {
                    $ipfsData = $response->json();
                    
                    if(isset($ipfsData['public_info'])) {
                        $info = $ipfsData['public_info'];
                        $isManipulated = false;

                        // Pengecekan Integritas Bukti Foto
                        $isBuktiHilang = false;
                        if ($statusBc == 2 && $program && empty($program->bukti_ipfs_hash) && !empty($buktiHash)) {
                            $isBuktiHilang = true;
                        }

                        // INSPEKSI MENDALAM (DEEP INSPECTION) LEVEL 1: Atribut Program
                        if (!$program) {
                            $isManipulated = true;
                        } else {
                            $dbTanggal   = Carbon::parse($program->tanggal_pelaksanaan)->format('Y-m-d');
                            $ipfsTanggal = Carbon::parse($info['tanggal_pelaksanaan'] ?? date('Y-m-d'))->format('Y-m-d');

                            // Sistem membandingkan SETIAP kolom. Jika ada satu saja yang berbeda, $isManipulated = true
                            if (
                                $program->judul !== $info['judul'] || 
                                (float)$program->dana_dibutuhkan !== (float)$info['dana_eth'] ||
                                $dbTanggal !== $ipfsTanggal ||
                                $program->bidang !== $info['bidang'] ||
                                $program->asnaf !== ($info['asnaf'] ?? 'Asnaf') ||
                                $program->sumber_dana !== ($info['sumber_dana'] ?? 'Zakat') ||
                                $program->bentuk_bantuan !== ($info['bentuk_bantuan'] ?? 'Bantuan Langsung') ||
                                $program->deskripsi_mustahik !== ($info['deskripsi_mustahik'] ?? '-') ||
                                $program->tipe_mustahik !== ($info['tipe_mustahik'] ?? 'umum') ||
                                $program->status !== $statusSeharusnya ||
                                $program->tx_hash !== $txHash ||
                                $program->proposal_ipfs_hash !== $proposalHash ||
                                $isBuktiHilang
                            ) {
                                $isManipulated = true;
                            }
                        }

                        // INSPEKSI MENDALAM LEVEL 2: Integritas Mustahik
                        // Mengecek setiap detail (NIK, Nama, Alamat, Bentuk Bantuan)
                        $rawMustahik = [];
                        if (!$isManipulated && isset($ipfsData['encrypted_detail'])) {
                            $decryptedString = Crypt::decryptString($ipfsData['encrypted_detail']);
                            $rawMustahik     = json_decode($decryptedString, true);

                            $existingMustahiks = Mustahik::where('distribution_id', $programId)->get();

                            // 1. Cek apakah jumlah mustahik dimanipulasi
                            if ($existingMustahiks->count() !== count($rawMustahik)) {
                                $isManipulated = true;
                            } else {
                                // 2. Cek ketat setiap kolom (NIK, Nama, Alamat, Bantuan)
                                $existingMustahiksKeyed = $existingMustahiks->keyBy('nik');
                                
                                foreach ($rawMustahik as $m) {
                                    $dbMustahik = $existingMustahiksKeyed->get($m['nik']);
                                    
                                    // Jika NIK tidak ditemukan di database
                                    if (!$dbMustahik) {
                                        $isManipulated = true;
                                        break;
                                    }

                                    // Jika Nama, Alamat, atau Bentuk Bantuan ada yang diubah oleh Hacker
                                    $bantuanAsli = $m['bantuan'] ?? 'Bantuan';
                                    if (
                                        $dbMustahik->nama !== $m['nama'] ||
                                        $dbMustahik->alamat !== $m['alamat'] ||
                                        $dbMustahik->bentuk_bantuan !== $bantuanAsli
                                    ) {
                                        $isManipulated = true;
                                        break;
                                    }
                                }
                            }
                        }

                        // JIKA TERDETEKSI 1 SAJA MANIPULASI -> EKSEKUSI SAPU BERSIH (HARD RESET)
                        if ($isManipulated) {
                            
                            DB::table('distributions')->updateOrInsert(
                                ['id' => $programId],
                                [
                                    'judul'               => $info['judul'],
                                    'deskripsi'           => $info['deskripsi'] ?? 'Dipulihkan dari Blockchain',
                                    'dana_dibutuhkan'     => $info['dana_eth'],
                                    'tanggal_pelaksanaan' => $info['tanggal_pelaksanaan'] ?? date('Y-m-d'),
                                    'bidang'              => $info['bidang'],
                                    'asnaf'               => $info['asnaf'] ?? 'Asnaf',
                                    'sumber_dana'         => $info['sumber_dana'] ?? 'Zakat',
                                    'bentuk_bantuan'      => $info['bentuk_bantuan'] ?? 'Bantuan Langsung',
                                    'deskripsi_mustahik'  => $info['deskripsi_mustahik'] ?? 'Dipulihkan oleh sistem Blockchain',
                                    'tipe_mustahik'       => $info['tipe_mustahik'] ?? 'umum',
                                    'status'              => $statusSeharusnya,
                                    'tx_hash'             => $txHash,
                                    'proposal_ipfs_hash'  => $proposalHash,
                                    'bukti_ipfs_hash'     => $statusBc == 2 ? $buktiHash : null, 
                                    'updated_at'          => now(),
                                    'created_at'          => $program ? $program->created_at : now(),
                                ]
                            );

                            // Decode ulang jika pada Inspeksi Level 2 belum sempat ter-decode (karena error di Level 1)
                            if (empty($rawMustahik) && isset($ipfsData['encrypted_detail'])) {
                                $decryptedString = Crypt::decryptString($ipfsData['encrypted_detail']);
                                $rawMustahik     = json_decode($decryptedString, true);
                            }

                            // Hapus seluruh mustahik fiktif dan tanam ulang yang asli dari IPFS
                            if (!empty($rawMustahik)) {
                                Mustahik::where('distribution_id', $programId)->delete();

                                $mustahikToInsert = [];
                                foreach ($rawMustahik as $m) {
                                    $mustahikToInsert[] = [
                                        'distribution_id' => $programId,
                                        'nik'             => $m['nik'],
                                        'nama'            => $m['nama'],
                                        'alamat'          => $m['alamat'],
                                        'bentuk_bantuan'  => $m['bantuan'] ?? 'Bantuan',
                                        'created_at'      => now(),
                                        'updated_at'      => now(),
                                    ];
                                }
                                Mustahik::insert($mustahikToInsert);
                            }

                            $diperbaiki++;
                        }
                    }
                }
                
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("[HEALING ERROR] Gagal memulihkan Program ID {$programId}: " . $e->getMessage());
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }

        return response()->json([
            'status'  => 'success', 
            'message' => "Audit Web3 Selesai. {$diperbaiki} program berhasil dipulihkan dari korupsi data."
        ]);
    }
}