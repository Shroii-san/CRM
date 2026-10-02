<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientSource;
use App\Models\Organization;
use App\Models\Province;
use App\Services\ClientExportService;
use App\Services\ClientService;
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

    public function search(Request $request, ClientService $clientService)
    {
        $user = Auth::user();

        if (!$this->permissionService->hasPermission($user, 'clients', 'view')) {
            abort(403, 'Anda tidak memiliki akses untuk pencarian client.');
        }

        $search = trim((string) $request->input('search', ''));
        $page = max(1, (int) $request->input('page', 1));
        $perPage = max(10, min(50, (int) $request->input('per_page', 10)));

        $clients = $clientService->list(
            user: $user,
            search: $search !== '' ? $search : null,
            perPage: $perPage,
            page: $page,
        );

        return response()->json([
            'items' => $clients->getCollection()->map(function (Client $client) {
                return [
                    'id' => $client->id,
                    'number' => $client->id,
                    'name' => $client->organization?->name ?? $client->person?->name ?? '-',
                    'type' => $client->organization_id ? 'Organization' : ($client->person_id ? 'Person' : '-'),
                    'email' => $client->organization?->email ?? $client->person?->email ?? '-',
                    'phone' => $client->organization?->phone ?? $client->person?->phone ?? '-',
                    'address' => $client->organization?->address ?? $client->person?->address ?? '-',
                    'status' => $client->is_active ? 'Active' : 'Inactive',
                    'sales' => '-',
                    'actions' => '',
                ];
            })->values()->all(),
            'pagination' => [
                'total' => $clients->total(),
                'per_page' => $clients->perPage(),
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage(),
                'has_more_pages' => $clients->hasMorePages(),
            ],
        ]);
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
