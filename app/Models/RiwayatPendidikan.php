<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikan extends Model
{
    protected $table = 'riwayat_pendidikans';

    protected $fillable = [
        'kandidat_id', 
        'karyawan_id',
        'jenjang', 
        'nama_sekolah', 
        'jurusan', 
        'gelar', 
        'ipk_atau_nilai', 
        'lokasi', 
        'tahun_masuk', 
        'tahun_lulus',
    ];

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}