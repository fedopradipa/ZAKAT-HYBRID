<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mustahik extends Model
{
  use HasFactory;

  protected $fillable = ['distribution_id', 'nik', 'nama', 'bentuk_bantuan', 'alamat'];

  public function distribution()
  {
    return $this->belongsTo(Distribution::class);
  }
}
