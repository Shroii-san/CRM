<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory, HasActivityLogs;

    // Sesuai migration: tabel bernama `menus`
    protected $table = 'menus';

    protected $fillable = [
        'parent_id',
        'icon_id',
        'name',
        'slug',
        'route',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];


    public function permissions()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'menu_id', 'role_id')
            ->using(RolePermission::class)
            ->withPivot('can_view', 'can_create', 'can_update', 'can_delete', 'can_assign')
            ->withTimestamps();
    }
    public function icon()
    {
        return $this->belongsTo(MenuIcon::class, 'icon_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id', 'id');
    }


    public function children()
    {
        // Migration menggunakan kolom `position` untuk ordering
        return $this->hasMany(Menu::class, 'parent_id', 'id')->orderBy('position');
    }


    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildrenOnly($query)
    {
        return $query->whereNotNull('parent_id');
    }


    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }


    public function getActiveChildren()
    {
        return $this->children()->where('is_active', true)->get();
    }

    public function getFullPath()
    {
        if ($this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }

    public function getLevel()
    {
        $level = 0;
        $parent = $this->parent;
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        return $level;
    }


    public function getAllDescendants()
    {
        $descendants = collect();
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }
        return $descendants;
    }


    public static function getMenuTree()
    {
        return self::with('children')
            ->whereNull('parent_id')
            ->orderBy('position')
            ->get();
    }
}
