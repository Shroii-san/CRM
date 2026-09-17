<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory, HasActivityLogs;

    protected $table = 'menu';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'icon_id',
        'name',
        'slug',
        'route',
        'position',
        'is_active',
    ];


    public function permissions()
    {
        return $this->belongsToMany(Menu::class, 'role_permissions', 'role_id', 'menu_id')
            ->using(RolePermission::class)
            ->withPivot('can_view', 'can_create', 'can_edit', 'can_delete', 'can_assign')
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
        return $this->hasMany(Menu::class, 'parent_id', 'id')->orderBy('order');
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
            return $this->parent->nama_menu . ' > ' . $this->nama_menu;
        }
        return $this->nama_menu;
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
            ->orderBy('order')
            ->get();
    }
}
