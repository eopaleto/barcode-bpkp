<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pulang extends Model
{
    protected $table = 'pulang';
    protected $guarded = [];
    protected $fillable = ['pegawai_id', 'jumlah_koper', 'foto_koper', 'barcode', 'status'];

    protected $casts = [
        'foto_koper' => 'array',
    ];
}
