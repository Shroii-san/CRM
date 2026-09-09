<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'role_id';
    public $timestamps = false;

    protected $fillable = ['role_name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menu', 'role_id', 'menu_id')
                    ->withPivot('can_view', 'can_create', 'can_edit', 'can_delete', 'can_assign')
                    ->withTimestamps();
    }
}