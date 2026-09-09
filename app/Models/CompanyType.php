<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
    protected $table = 'company_type';
    protected $primaryKey = 'company_type_id';
    public $timestamps = false;

    protected $fillable = [
        'type_name', 'description', 'is_active'
    ];

    public function companies()
    {
        return $this->hasMany(Company::class, 'company_type_id', 'company_type_id');
    }
}