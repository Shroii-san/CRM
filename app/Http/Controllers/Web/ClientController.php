<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientSource;
use App\Models\Organization;
use App\Models\Province;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Tampilkan halaman utama Manajemen Client (Blade View).
     */
    public function index(Request $request)
    {
        $customers = Client::with(['person', 'organization', 'source'])->paginate(10);
        $sources = ClientSource::where('is_active', true)->get();
        $companies = Organization::orderBy('name', 'asc')->get();
        $provinces = Province::orderBy('name', 'asc')->get();

        return view('pages.customers', compact('customers', 'sources', 'companies', 'provinces'));
    }
}
