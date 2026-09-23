<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Tampilkan halaman utama Manajemen Menu (Blade View).
     */
    public function index(Request $request)
    {
        $menus = Menu::with(['permissions', 'parent'])->orderBy('position', 'asc')->paginate(10);
        $roles = Role::all();

        return view('pages.menu', compact('menus', 'roles'));
    }
}
