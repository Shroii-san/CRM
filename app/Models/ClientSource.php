<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class ClientSource extends Model
{

    use HasActivityLogs;

    protected $table = 'client_sources';

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
