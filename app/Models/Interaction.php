<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{

    use HasActivityLogs;

    protected $table = 'interactions';

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

    protected $casts = [
        'type' => 'integer',
        'status' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
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
