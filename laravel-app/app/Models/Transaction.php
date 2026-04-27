<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'wallet_address', // Identitas tunggal
        'jenis_dana',
        'nominal',
        'nominal_bersih',
        'hak_amil',
        'tx_hash',
        'is_verified',
        'verified_at',
        'metadata',
    ];

    protected $casts = [
        'metadata'    => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // ⭐ RELASI WEB3: Laravel akan mencari User yang memiliki wallet_address yang sama
    public function user()
    {
        return $this->belongsTo(User::class, 'wallet_address', 'wallet_address');
    }
}