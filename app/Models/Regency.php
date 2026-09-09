<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    protected $fillable = ['id', 'province_id', 'name'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'regency_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'regency_id', 'id');
    }
}