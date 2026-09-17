<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    use HasActivityLogs;

    protected $table = 'clients';
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

    public function deals()
    {
        return $this->hasMany(Deal::class, 'client_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'client_id', 'id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'note_id', 'id');
    }

    public function externalConversation()
    {
        return $this->hasMany(ExternalConversation::class, 'client_id', 'id');
    }

    public function attachable()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
