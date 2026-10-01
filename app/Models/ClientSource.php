<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class ClientSource extends Model
{
    use HasActivityLogs, Filterable;

    protected $table = 'client_sources';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function clients()
    {
        return $this->hasMany(Client::class, 'source_id', 'id');
    }
}
