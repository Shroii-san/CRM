<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $fillable = ['id', 'district_id', 'name'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'village_id', 'id');
    }
}