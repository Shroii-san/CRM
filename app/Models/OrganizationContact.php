<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class OrganizationContact extends Model
{

    use HasActivityLogs;

    protected $table = 'organization_contacts';

    protected $fillable = [
        'organization_id',
        'person_id',
        'job_title',
        'is_primary',
        'started_at',
        'ended_at',
    ];

    //FK relationships untuk PIC/Contact Person organisasi
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    //FK relationships untuk orang yang jadi PIC/Contact Person organisasi
    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'organization_contact_id', 'id');
    }
}
