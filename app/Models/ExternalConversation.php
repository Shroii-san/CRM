<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class ExternalConversation extends Model
{

    use HasActivityLogs;

    protected $table = 'external_conversations';

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
