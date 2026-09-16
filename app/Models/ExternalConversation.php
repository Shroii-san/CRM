<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalConversation extends Model
{
    protected $table = 'external_conversations';

    protected $primaryKey = 'id';

    protected $fillable = [
        'platform',
        'external_conversation_id',
        'client_id',
        'last_interaction_at',
        'metadata'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function attachable()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
