<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSource extends Model
{
    protected $table = 'client_sources';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class, 'source_id', 'id');
    }
}
