<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Tampilkan halaman utama Manajemen Role (Blade View).
     */
    public function index(Request $request)
    {
        $roles = Role::paginate(10);
        $users = User::all();
        $menus = Menu::all();

        return view('pages.role', compact('roles', 'users', 'menus'));
    }
}
