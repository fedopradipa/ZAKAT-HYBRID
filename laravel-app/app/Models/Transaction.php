<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_dana',
        'nominal',
        'tx_hash',
        'metadata' // ✅ Izinkan penyimpanan metadata
    ];

    // ✅ Beritahu Laravel untuk otomatis convert JSON ke Array (dan sebaliknya)
    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}