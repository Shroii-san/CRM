<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPic extends Model
{
    use HasFactory;

    protected $table = 'company_pics';
    protected $primaryKey = 'pic_id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Tambahkan ini untuk menonaktifkan timestamps
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'pic_name',
        'position',
        'phone',
        'email',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function salesVisits()
    {
        return $this->hasMany(SalesVisit::class, 'pic_id', 'pic_id');
    }
}
