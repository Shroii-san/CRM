<?php

namespace App\Services;

use App\Models\Menu;

class MenuService
{
    /**
     * Mendapatkan daftar menu dengan pagination & filter.
     */
    public function list(
        ?string $search = null,
        ?bool $parentOnly = false,
        int $perPage = 50,
        int $page = 1
    ) {
        $query = Menu::query()
            ->with(['parent', 'icon', 'children'])
            ->orderBy('position', 'asc');

        if ($parentOnly) {
            $query->whereNull('parent_id');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('route', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Mendapatkan struktur pohon menu (Menu Tree).
     */
    public function getMenuTree()
    {
        return Menu::getMenuTree();
    }

    /**
     * Membuat menu baru.
     */
    public function create(array $data): Menu
    {
        if (!empty($data['route'])) {
            $data['route'] = ltrim($data['route'], '/');
        }

        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        $data['position'] = $data['position'] ?? ($data['order'] ?? (Menu::max('position') + 1));

        return Menu::create([
            'parent_id' => $data['parent_id'] ?? null,
            'icon_id'   => $data['icon_id'] ?? null,
            'name'      => $data['name'] ?? $data['nama_menu'],
            'slug'      => $data['slug'],
            'route'     => $data['route'] ?? null,
            'position'  => $data['position'],
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Memperbarui menu yang ada.
     */
    public function update(int $id, array $data): Menu
    {
        $menu = Menu::findOrFail($id);

        if (!empty($data['route'])) {
            $data['route'] = ltrim($data['route'], '/');
        }

        // Cegah menu menjadi parent dari dirinya sendiri
        if (isset($data['parent_id']) && $data['parent_id'] == $menu->id) {
            throw new \InvalidArgumentException('Menu tidak bisa menjadi parent dari dirinya sendiri!');
        }

        // Cegah circular reference
        if (!empty($data['parent_id']) && $this->wouldCreateCircularReference($menu->id, $data['parent_id'])) {
            throw new \InvalidArgumentException('Tidak bisa membuat referensi melingkar pada menu!');
        }

        $position = $data['position'] ?? ($data['order'] ?? $menu->position);

        $menu->update([
            'parent_id' => $data['parent_id'] ?? null,
            'icon_id'   => $data['icon_id'] ?? null,
            'name'      => $data['name'] ?? ($data['nama_menu'] ?? $menu->name),
            'slug'      => $data['slug'] ?? $menu->slug,
            'route'     => $data['route'] ?? null,
            'position'  => $position,
            'is_active' => $data['is_active'] ?? $menu->is_active,
        ]);

        return $menu->refresh();
    }

    /**
     * Menghapus menu.
     */
    public function delete(int $id): void
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
    }

    /**
     * Memeriksa referensi melingkar parent-child menu.
     */
    private function wouldCreateCircularReference(int $menuId, int $newParentId): bool
    {
        $currentParent = Menu::find($newParentId);
        while ($currentParent) {
            if ($currentParent->id == $menuId) {
                return true;
            }
            $currentParent = $currentParent->parent;
        }
        return false;
    }
}
