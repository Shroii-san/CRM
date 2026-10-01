<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kandidat_id', 'departemen_id', 'nama_lengkap', 'nik', 'no_telepon',
        'tanggal_masuk', 'status_kerja', 'foto', 'scan_ktp', 'cv', 'npwp', 'no_bpjs',
    ];

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class);
    }

    public function pendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class, 'karyawan_id');
    }

    public function keluarga()
    {
        return $this->hasMany(Keluarga::class, 'karyawan_id');
    }
}
