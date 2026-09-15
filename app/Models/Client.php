<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'person_id',
        'organization_id',
        'source_id',
        'is_active',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function source()
    {
        return $this->belongsTo(ClientSource::class, 'source_id', 'id');
    }
}