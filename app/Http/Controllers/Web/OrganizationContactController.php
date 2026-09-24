<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationContact;
use Illuminate\Http\Request;

class OrganizationContactController extends Controller
{
    /**
     * Tampilkan halaman utama Manajemen PIC (Blade View).
     */
    public function index(Request $request)
    {
        $pics = OrganizationContact::with(['organization', 'person'])->paginate(10);
        $companies = Organization::orderBy('name', 'asc')->get();

        return view('pages.pic', compact('pics', 'companies'));
    }
}
