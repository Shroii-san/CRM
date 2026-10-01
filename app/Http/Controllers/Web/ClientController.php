<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientSource;
use App\Models\Organization;
use App\Models\Province;
use App\Services\ClientExportService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    /**
     * Tampilkan halaman utama Manajemen Client (Blade View).
     */
    public function index(Request $request)
    {
        $clients = Client::with(['person', 'organization', 'source'])->paginate(10);
        $sources = ClientSource::where('is_active', true)->get();
        $companies = Organization::orderBy('name', 'asc')->get();
        $provinces = Province::orderBy('name', 'asc')->get();

        return view('pages.clients', compact('clients', 'sources', 'companies', 'provinces'));
    }

    public function export(ClientExportService $exportService)
    {
        $user = Auth::user();

        if (!$this->permissionService->hasPermission($user, 'clients', 'view')) {
            abort(403, 'Anda tidak memiliki akses untuk export client.');
        }

        return $exportService->exportCsv($user);
    }
}
