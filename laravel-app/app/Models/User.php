<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'wallet_address',
        'role',
    ];

    protected $hidden = [
        'remember_token',
    ];

    // Hapus blok protected $casts yang berisi email_verified_at dan hashed password
}