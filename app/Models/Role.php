<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    use HasActivityLogs;

    protected $table = 'roles';

    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Menu::class, 'role_permissions', 'role_id', 'menu_id')
            ->using(RolePermission::class)
            ->withPivot('can_view', 'can_create', 'can_update', 'can_delete', 'can_assign')
            ->withTimestamps();
    }
}
