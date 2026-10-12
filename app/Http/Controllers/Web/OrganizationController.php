<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Organization;
use App\Models\OrganizationContact;
use App\Models\Province;
use App\Services\OrganizationExportService;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct(private OrganizationExportService $exportService)
    {
    }

    /**
     * Tampilkan halaman utama Manajemen Organisasi (Blade View).
     */
    public function index(Request $request)
    {
        $organizations = Organization::with(['industry', 'province', 'regency', 'district', 'village'])
            ->paginate(10);
        $industries = Industry::where('is_active', true)->orderBy('name')->get();
        $provinces  = Province::orderBy('name', 'asc')->get();

        $totalOrganizations = Organization::count();
        $totalIndustry      = Organization::distinct()->count('industry_id');
        $tierOrganization   = Organization::distinct()->count('tier');
        $activeOrganization = Organization::where('is_active', true)->count();

        return view('pages.organizations', array_merge(
            compact('industries', 'provinces', 'totalOrganizations', 'totalIndustry', 'tierOrganization', 'activeOrganization'),
            ['companies' => $organizations, 'organizations' => $organizations]
        ));
    }

    /**
     * Tampilkan detail satu organisasi.
     * Mendukung permintaan JSON (AJAX) maupun blade view.
     */
    public function show(Request $request, $id)
    {
        $organization = Organization::with(['industry', 'province', 'regency', 'district', 'village'])->findOrFail($id);

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'company' => $organization,
            ]);
        }

        return view('pages.organization-show', compact('organization'));
    }

    /**
     * Kembalikan daftar PIC (contacts) dari satu organisasi (JSON).
     */
    public function pics($id)
    {
        $organization = Organization::with('contacts.person')->findOrFail($id);

        return response()->json([
            'success' => true,
            'pics'    => $organization->contacts->map(function ($contact) {
                return [
                    'id'    => $contact->id,
                    'name'  => $contact->person?->name ?? '-',
                    'role'  => $contact->job_title ?? '-',
                    'phone' => $contact->person?->phone ?? '-',
                    'email' => $contact->person?->email ?? '-',
                ];
            })->values(),
        ]);
    }

    /**
     * Cari organisasi dengan pagination (AJAX / JSON).
     */
    public function search(Request $request)
    {
        $search  = trim((string) $request->input('search', ''));
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = max(10, min(50, (int) $request->input('per_page', 10)));

        $query = Organization::query()->with(['industry', 'province', 'regency', 'district', 'village']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $organizations = $query->orderBy('name', 'asc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'items'      => $organizations->items(),
            'pagination' => [
                'total'        => $organizations->total(),
                'per_page'     => $organizations->perPage(),
                'current_page' => $organizations->currentPage(),
                'last_page'    => $organizations->lastPage(),
                'has_more_pages' => $organizations->hasMorePages(),
            ],
        ]);
    }

    /**
     * Simpan organisasi baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'industry_id' => ['required', 'integer', 'exists:industries,id'],
            'tier'        => ['nullable', 'string', 'max:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'website'     => ['nullable', 'url', 'max:255'],
            'is_active'   => ['nullable', 'boolean'],
            'address'     => ['nullable', 'string'],
            'province_id' => ['nullable', 'string'],
            'regency_id'  => ['nullable', 'string'],
            'district_id' => ['nullable', 'string'],
            'village_id'  => ['nullable', 'string'],
        ]);

        $organization = Organization::create($data);

        return response()->json([
            'success'      => true,
            'message'      => 'Organization created successfully.',
            'organization' => $organization,
        ], 201);
    }

    /**
     * Perbarui data organisasi.
     */
    public function update(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);

        $data = $request->validate([
            'name'        => ['nullable', 'string', 'max:255'],
            'industry_id' => ['nullable', 'integer', 'exists:industries,id'],
            'tier'        => ['nullable', 'string', 'max:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'website'     => ['nullable', 'url', 'max:255'],
            'is_active'   => ['nullable', 'boolean'],
            'address'     => ['nullable', 'string'],
            'province_id' => ['nullable', 'string'],
            'regency_id'  => ['nullable', 'string'],
            'district_id' => ['nullable', 'string'],
            'village_id'  => ['nullable', 'string'],
        ]);

        $organization->update($data);

        return response()->json([
            'success'      => true,
            'message'      => 'Organization updated successfully.',
            'organization' => $organization,
        ]);
    }

    /**
     * Hapus organisasi.
     */
    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();

        return response()->json([
            'success' => true,
            'message' => 'Organization deleted successfully.',
        ]);
    }

    // =========================================================
    // Export Methods – didelegasikan ke OrganizationExportService
    // =========================================================

    /**
     * Ekspor profil detail satu organisasi ke PDF.
     */
    public function exportPdf($id)
    {
        return $this->exportService->exportPdf((int) $id);
    }

    /**
     * Ekspor daftar organisasi yang difilter berdasarkan industri dan/atau tier ke PDF.
     */
    public function exportPdfList(Request $request)
    {
        $industryId = $request->filled('industry_id') ? (int) $request->input('industry_id') : null;
        $tier       = $request->filled('tier') ? $request->input('tier') : null;

        return $this->exportService->exportPdfList($industryId, $tier);
    }

    /**
     * Ekspor daftar organisasi yang dipilih secara manual ke PDF.
     */
    public function exportPdfSelected(Request $request)
    {
        $ids = array_filter(explode(',', $request->query('ids', '')));

        return $this->exportService->exportPdfSelected($ids);
    }

    /**
     * Ekspor rekap pivot organisasi per tier ke Excel.
     */
    public function exportPivotExcel()
    {
        return $this->exportService->exportPivotExcel();
    }
}
