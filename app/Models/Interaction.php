<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    protected $table = 'interactions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'client_id',
        'deal_id',
        'organization_contact_id',
        'type',
        'subject',
        'description',
        'summary',
        'status',
        'start_at',
        'end_at',
        'performed_by',
        'external_reference'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id', 'id');
    }

    public function organizationContacts()
    {
        return $this->belongsTo(OrganizationContact::class, 'organizaton_contact_id', 'id');
    }

    public function attachable()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
