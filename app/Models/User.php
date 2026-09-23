<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasActivityLogs, Notifiable, HasFactory;

    protected $table = 'users';
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'password_hash',
        'is_active',
        'last_activity_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    protected $hidden = ['password_hash', 'remember_token'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'assigned_user_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_user_id', 'id');
    }

    public function changeStageHistories()
    {
        return $this->hasMany(DealStageHistory::class, 'changed_by_user_id', 'id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'created_by', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_by', 'id');
    }

    public function attachable()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = Hash::make($value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower(str_replace(' ', '_', $value));
    }

    public function getNameAttribute($value)
    {
        return strtoupper(str_replace('_', ' ', $value));
    }
}

