<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pegawai extends Model
{
    use HasFactory;
    protected $table = 'pegawai';
    protected $fillable = [
        'nip_lama',
        'nip_baru',
        'nama',
        'unit_kerja',
        'jabatan'
    ];

    public function pergi()
    {
        return $this->hasMany(Pergi::class);
    }
}
