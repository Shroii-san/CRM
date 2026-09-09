<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'type',
        'email',
        'phone',
        'address',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'status',
        'source',
        'pic',
        'notes',
        'contact_person_name',
        'contact_person_email',
        'contact_person_phone',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Boot untuk generate customer_id otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->customer_id)) {
                $model->customer_id = 'CUST-' . strtoupper(Str::random(8));
            }
        });
    }

    // Relationships untuk Address
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }

    // Accessor untuk full address
    public function getFullAddressAttribute()
    {
        $addressParts = collect([
            $this->address,
            optional($this->village)->name,
            optional($this->district)->name,
            optional($this->regency)->name,
            optional($this->province)->name,
        ])->filter()->implode(', ');

        return $addressParts ?: '-';
    }

    // Scope untuk filter
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('customer_id', 'like', "%{$search}%");
    }

     public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}