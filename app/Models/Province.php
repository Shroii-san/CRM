<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = ['id', 'name'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function regencies()
    {
        return $this->hasMany(Regency::class, 'province_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'province_id', 'id');
    }
}