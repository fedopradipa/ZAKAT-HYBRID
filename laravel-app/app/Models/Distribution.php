<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;

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
        'tx_hash',
        'ipfs_hash',
        'konfirmasi_status',
    ];

    // Menerapkan Best Practice: Casting tipe data
    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'ipfs_hash'           => 'array', // <-- PENTING: Ubah string menjadi array
    ];

    public function mustahiks()
    {
        return $this->hasMany(Mustahik::class);
    }
}