<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function regencies()
    {
        return $this->hasMany(Regency::class, 'province_id', 'id');
    }
}