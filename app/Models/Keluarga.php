<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    use HasFactory;

    protected $table = 'keluarga';

    protected $fillable = [
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'status',
        'karyawan_id',
        'kandidat_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relationship ke Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // Relationship ke Kandidat
    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class, 'kandidat_id');
    }

    // Auto hitung usia
    public function getUsiaAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal_lahir)->age;
    }
}
