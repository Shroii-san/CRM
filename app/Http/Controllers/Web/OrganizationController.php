<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Organization;
use App\Models\Province;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Tampilkan halaman utama Manajemen Organisasi (Blade View).
     */
    public function index(Request $request)
    {
        $companies = Organization::with(['industry', 'province', 'regency', 'district', 'village'])
            ->paginate(10);
        $types = Industry::where('is_active', true)->get();
        $provinces = Province::orderBy('name', 'asc')->get();

        return view('pages.company', compact('companies', 'types', 'provinces'));
    }

    /**
     * Detail halaman organisasi.
     */
    public function show($id)
    {
        $company = Organization::with(['industry', 'province', 'regency', 'district', 'village'])->findOrFail($id);

        return view('pages.company-show', compact('company'));
    }
}
