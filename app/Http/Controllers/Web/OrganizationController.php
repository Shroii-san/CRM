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
        $organizations = Organization::with(['industry', 'province', 'regency', 'district', 'village'])
            ->paginate(10);
        $industries = Industry::where('is_active', true)->orderBy('name')->get();
        $provinces = Province::orderBy('name', 'asc')->get();

        $totalOrganizations = Organization::count();
        $totalIndustry = Organization::distinct()->count('industry_id');
        $tierOrganization = Organization::distinct()->count('tier');
        $activeOrganization = Organization::where('is_active', true)->count();

        return view('pages.organizations', array_merge(
            compact('industries', 'provinces', 'totalOrganizations', 'totalIndustry', 'tierOrganization', 'activeOrganization'),
            ['companies' => $organizations, 'organizations' => $organizations]
        ));
    }

    /**
     * Detail halaman organisasi.
     */
    public function show(Request $request, $id)
    {
        $company = Organization::with(['industry', 'province', 'regency', 'district', 'village'])->findOrFail($id);

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'company' => $company,
            ]);
        }

        return view('pages.company-show', compact('company'));
    }

    public function pics($id)
    {
        $organization = Organization::with('contacts.person')->findOrFail($id);

        return response()->json([
            'success' => true,
            'pics' => $organization->contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->person?->name ?? $contact->name ?? '-',
                    'role' => $contact->job_title ?? '-',
                    'phone' => $contact->person?->phone ?? '-',
                    'email' => $contact->person?->email ?? '-',
                ];
            })->values(),
        ]);
    }

    public function search(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $page = max(1, (int) $request->input('page', 1));
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
            'items' => $organizations->items(),
            'pagination' => [
                'total' => $organizations->total(),
                'per_page' => $organizations->perPage(),
                'current_page' => $organizations->currentPage(),
                'last_page' => $organizations->lastPage(),
                'has_more_pages' => $organizations->hasMorePages(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry_id' => ['required', 'integer', 'exists:industries,id'],
            'tier' => ['nullable', 'string', 'max:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'address' => ['nullable', 'string'],
            'province_id' => ['nullable', 'string'],
            'regency_id' => ['nullable', 'string'],
            'district_id' => ['nullable', 'string'],
            'village_id' => ['nullable', 'string'],
        ]);

        $organization = Organization::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Organization created successfully.',
            'organization' => $organization,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'industry_id' => ['nullable', 'integer', 'exists:industries,id'],
            'tier' => ['nullable', 'string', 'max:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'address' => ['nullable', 'string'],
            'province_id' => ['nullable', 'string'],
            'regency_id' => ['nullable', 'string'],
            'district_id' => ['nullable', 'string'],
            'village_id' => ['nullable', 'string'],
        ]);

        $organization->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Organization updated successfully.',
            'organization' => $organization,
        ]);
    }

    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();

        return response()->json([
            'success' => true,
            'message' => 'Organization deleted successfully.',
        ]);
    }
}
