<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{

    use HasActivityLogs;

    protected $table = 'persons';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    // FK relationships untuk orang yang jadi PIC/Contact Person organisasi
    public function organizationContacts()
    {
        return $this->hasMany(OrganizationContact::class, 'person_id', 'id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'person_id', 'id');
    }
}
