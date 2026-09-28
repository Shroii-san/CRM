<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    public $timestamps = false;

    public function provinces()
    {
        return $this->hasMany(Province::class, 'country_id', 'id');
    }
}
