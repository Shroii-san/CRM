<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasActivityLogs, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'password_hash',
        'is_active',
    ];

    protected $hidden = ['password_hash'];

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

    public function attachment()
    {
        return $this->hasMany(Attachment::class, 'uploaded_by', 'id');
    }

    public function attachable()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }


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

    public function getAuthPassword()
    {
        return $this->password_hash;
    }


    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = strtolower(str_replace(' ', '_', $value));
    }

    public function getUsernameAttribute($value)
    {
        return strtoupper(str_replace('_', ' ', $value));
    }

    public function canAccess($menuId, string $action): bool
    {
        // 1. Superadmin Bypass
        if ($this->role?->role_name === 'superadmin') {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        $menu = $this->role->relationLoaded('menus')
            ? $this->role->menus->firstWhere('id', $menuId)
            : $this->role->menus()->where('menus.id', $menuId)->first();

        if (!$menu) {
            return false;
        }

        $pivotColumn = 'can_' . $action;

        return $menu->pivot->{$pivotColumn} ?? false;
    }

    public function canAccessCurrent(string $action): bool
    {
        $menuId = currentMenuId();

        return $menuId ? $this->canAccess($menuId, $action) : false;
    }

    public function hasAnyAccess($menuId): bool
    {
        if ($this->role?->role_name === 'superadmin') {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        $menu = $this->role->relationLoaded('menus')
            ? $this->role->menus->firstWhere('id', $menuId)
            : $this->role->menus()->where('menus.id', $menuId)->first();

        if (!$menu) {
            return false;
        }

        $pivot = $menu->pivot;

        return $pivot->can_view || $pivot->can_create || $pivot->can_edit || $pivot->can_delete;
    }
}
