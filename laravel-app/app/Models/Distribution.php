<?php
// app/Models/Distribution.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'distributions';

    // Semua kolom yang diizinkan untuk diisi (Mass Assignment)
    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal_pelaksanaan',
        'dana_dibutuhkan',
        'bidang',
        'sumber_dana',
        'asnaf',
        'bentuk_bantuan',
        'deskripsi_mustahik',
        'tipe_mustahik',
        'status',
        
        // ── BUKTI WEB3 ──
        'tx_hash',
        'proposal_ipfs_hash',
        'bukti_ipfs_hash',
    ];

    // Casting tipe data
    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'dana_dibutuhkan'     => 'decimal:8',
        // Bukti IPFS berupa array JSON foto-foto (diubah otomatis ke array PHP)
        'bukti_ipfs_hash'     => 'array', 
    ];

    // Relasi ke tabel Mustahiks
    public function mustahiks()
    {
        return $this->hasMany(Mustahik::class, 'distribution_id', 'id');
    }

    /* * ====================================================================
     * GEMBOK ANTI-GIGO TELAH DIHAPUS.
     * Sistem perlindungan kini sepenuhnya dialihkan ke WebhookController
     * (Single Source of Truth dari Blockchain).
     * ====================================================================
     */
}