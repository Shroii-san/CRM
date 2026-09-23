<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Tampilkan halaman utama Manajemen User (Blade View).
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('name', 'asc')->get();

        return view('pages.user', compact('roles'));
    }
}
