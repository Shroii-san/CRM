<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Kandidat extends Model
{
     use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'nik',
        'no_telepon',
        'divisi',
        'tgl_lamar',
        'foto',
        'scan_ktp',
        'cv',
        'npwp',
        'no_bpjs',
        'status',
    ];

    public function pendidikan()
{
    return $this->hasMany(RiwayatPendidikan::class, 'kandidat_id');
}

public function karyawan()
{
    return $this->hasOne(Karyawan::class);
}

public function keluarga()
{
    return $this->hasMany(Keluarga::class, 'kandidat_id');
}


}
