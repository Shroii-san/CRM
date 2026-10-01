<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasActivityLogs, Filterable;

    protected $table = 'industries';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // FK relationships untuk organisasi yang termasuk dalam jenis industri ini
    public function organizations()
    {
        return $this->hasMany(Organization::class, 'industry_id', 'id');
    }
}
