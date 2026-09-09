<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Role;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['roles', 'parent'])->orderBy('order', 'asc')->paginate(5);
        $roles = Role::all();
        
        return view('pages.menu', compact('menus', 'roles'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $data = $request->validate([
        'nama_menu' => 'required|string|max:100',
        'route'     => 'nullable|string|max:255',
        'icon'      => 'nullable|string|max:50',
        'order'     => 'nullable|integer|min:1',
        'parent_id' => 'nullable|exists:menu,menu_id'
    ]);

        if (!empty($data['route'])) {
        $data['route'] = ltrim($data['route'], '/');
    }

    // Kalau order kosong → taruh di paling bawah
        $data['order'] = $data['order'] ?? (Menu::max('order') + 1);
        $menu = Menu::create($data);

        return redirect()->route('menu')->with('success', 'Menu berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $data = $request->validate([
        'nama_menu' => 'required|string|max:100',
        'route'     => 'nullable|string|max:255',
        'icon'      => 'nullable|string|max:50',
        'order'     => 'nullable|integer|min:1',
        'parent_id' => 'nullable|exists:menu,menu_id'
    ]);


        if (!empty($data['route'])) {
        $data['route'] = ltrim($data['route'], '/');
    }

    // TAMBAH VALIDASI: Cegah menu jadi parent dari dirinya sendiri
        if ($data['parent_id'] == $menu->menu_id) {
            return redirect()->route('menu')->with('error', 'Menu tidak bisa menjadi parent dari dirinya sendiri!');
        }

        // TAMBAH VALIDASI: Cegah circular reference (child jadi parent dari parent-nya)
        if ($data['parent_id'] && $this->wouldCreateCircularReference($menu->menu_id, $data['parent_id'])) {
            return redirect()->route('menu')->with('error', 'Tidak bisa membuat referensi melingkar pada menu!');
        }

    // Kalau order kosong → taruh di paling bawah
        $data['order'] = $data['order'] ?? (Menu::max('order') + 1);

        $menu->update($data);

        return redirect()->route('menu')->with('success', 'Menu berhasil diperbarui');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        if ($menu->hasChildren()) {
            return redirect()->route('menu')->with('error', 'Tidak bisa menghapus menu yang masih memiliki sub-menu!');
        }

        return redirect()->route('menu')->with('success', 'Menu berhasil dihapus');
    }
    private function wouldCreateCircularReference($menuId, $parentId)
    {
        if (!$parentId) return false;

        $parent = Menu::find($parentId);
        while ($parent) {
            if ($parent->menu_id == $menuId) {
                return true;
            }
            $parent = $parent->parent;
        }
        return false;
    }

    // TAMBAH FUNCTION INI: API endpoint untuk ambil parent menu (buat AJAX kalau diperlukan)
    public function getParentMenus()
    {
        $parentMenus = Menu::whereNull('parent_id')
                          ->select('menu_id', 'nama_menu')
                          ->orderBy('nama_menu')
                          ->get();
        
        return response()->json($parentMenus);
    }

    /**
 * Search & Filter Menu (AJAX)
 */
public function search(Request $request)
{
    $query = Menu::with('parent', 'roles')->orderBy('order', 'asc');

    // === FILTER SEARCH (by nama_menu, route, icon) ===
    if ($request->filled('search')) {
        $search = strtolower($request->search);
        $query->where(function($q) use ($search) {
            $q->whereRaw('LOWER(nama_menu) LIKE ?', ["%{$search}%"])
              ->orWhereRaw('LOWER(route) LIKE ?', ["%{$search}%"])
              ->orWhereRaw('LOWER(icon) LIKE ?', ["%{$search}%"]);
        });
    }

    // === FILTER PARENT ===
    if ($request->filled('parent')) {
        $parent = strtolower($request->parent);
        if ($parent === 'root') {
            $query->whereNull('parent_id');
        } else {
            $query->whereHas('parent', function($q) use ($parent) {
                $q->whereRaw('LOWER(nama_menu) LIKE ?', ["%{$parent}%"]);
            });
        }
    }

    // === FILTER ROLE (opsional: jika menu punya relasi ke role) ===
    if ($request->filled('role')) {
        $role = strtolower($request->role);
        $query->whereHas('roles', function($q) use ($role) {
            $q->whereRaw('LOWER(role_name) LIKE ?', ["%{$role}%"]);
        });
    }

    $menus = $query->paginate(5);

    return response()->json([
        'items' => $menus->map(function($menu, $index) use ($menus) {
            return [
                'number' => $menus->firstItem() + $index,
                'nama_menu' => $menu->nama_menu ?? '-',
                'route' => $menu->route ?? '-',
                'icon' => $menu->icon ?? '-',
                'parent' => $menu->parent ? $menu->parent->nama_menu : 'Root',
                'order' => $menu->order ?? '-',
                'actions' => $this->getMenuActions($menu),
            ];
        })->toArray(),
        'pagination' => [
            'current_page' => $menus->currentPage(),
            'last_page' => $menus->lastPage(),
            'from' => $menus->firstItem(),
            'to' => $menus->lastItem(),
            'total' => $menus->total(),
        ]
    ]);
}

/**
 * Aksi untuk tiap baris menu
 */
private function getMenuActions($menu)
{
    $canEdit = auth()->user()->canAccess($currentMenuId ?? 1, 'edit');
    $canDelete = auth()->user()->canAccess($currentMenuId ?? 1, 'delete');

    $actions = [];

    if ($canEdit) {
        $actions[] = [
            'type' => 'edit',
            'onclick' => "openEditModal(
                '{$menu->menu_id}',
                '" . addslashes($menu->nama_menu) . "',
                '" . addslashes($menu->route ?? '') . "',
                '" . addslashes($menu->icon ?? '') . "',
                '{$menu->order}',
                '{$menu->parent_id}'
            )",
            'title' => 'Edit Menu'
        ];
    }

    if ($canDelete) {
        $csrfToken = csrf_token();
        $deleteRoute = route('menu.destroy', $menu->menu_id);

        $actions[] = [
            'type' => 'delete',
            'onclick' => "deleteMenu('{$menu->menu_id}', '{$deleteRoute}', '{$csrfToken}')",
            'title' => 'Delete Menu'
        ];
    }

    return $actions;
}



    
}