<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuIcon extends Model
{
    protected $table = 'menu_icons';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'class_name',
        'is_active',
    ];

    public function menu()
    {
        return $this->hasOne(Menu::class, 'icon_id', 'id');
    }
}
