<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Organization;
use App\Models\Province;
use App\Services\organization\OrganizationExportService;
use App\Services\organization\OrganizationImportService;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct(private OrganizationExportService $exportService, private OrganizationImportService $importService)
    {
    }

    /**
     * Tampilkan halaman utama Manajemen Organisasi (Blade View).
     * Data tabel dimuat secara AJAX melalui API, sehingga hanya data KPI
     * dan master referensi (industri, provinsi, daftar nama org) yang diambil di sini.
     */
    public function index()
    {
        $industries = Industry::where('is_active', true)->orderBy('name')->get();
        $provinces = Province::orderBy('name', 'asc')->get();

        $totalOrganizations = Organization::count();
        $totalIndustry = Organization::distinct()->count('industry_id');
        $tierOrganization = Organization::distinct()->count('tier');
        $activeOrganization = Organization::where('is_active', true)->count();

        // Hanya id & name untuk opsi dropdown export PDF
        $allOrganizations = Organization::orderBy('name', 'asc')->get(['id', 'name']);

        return view('pages.organizations', compact(
            'industries',
            'provinces',
            'totalOrganizations',
            'totalIndustry',
            'tierOrganization',
            'activeOrganization',
            'allOrganizations',
        ));
    }

    /**
     * Endpoint JSON opsi nama organisasi untuk dropdown export PDF di frontend.
     */
    public function options()
    {
        return response()->json([
            'success' => true,
            'data' => Organization::orderBy('name', 'asc')->get(['id', 'name']),
        ]);
    }

    /**
     * Kembalikan daftar PIC (contacts) dari satu organisasi (JSON).
     * Digunakan oleh modal detail di frontend.
     */
    public function pics($id)
    {
        $organization = Organization::with('contacts.person')->findOrFail($id);

        return response()->json([
            'success' => true,
            'pics' => $organization->contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->person?->name ?? '-',
                    'role' => $contact->job_title ?? '-',
                    'phone' => $contact->person?->phone ?? '-',
                    'email' => $contact->person?->email ?? '-',
                ];
            })->values(),
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
        $tier = $request->filled('tier') ? $request->input('tier') : null;

        return $this->exportService->exportPdfList($industryId, $tier);
    }

    /**
     * Ekspor daftar organisasi yang dipilih secara manual ke PDF.
     */
    public function exportPdfSelected(Request $request)
    {
        $rawIds = $request->query('ids', '');
        if (is_array($rawIds)) {
            $ids = array_map('intval', array_filter($rawIds));
        } else {
            $ids = array_map('intval', array_filter(explode(',', (string) $rawIds)));
        }

        return $this->exportService->exportPdfSelected($ids);
    }

    /**
     * Ekspor rekap pivot organisasi per tier ke Excel.
     */
    public function exportPivotExcel()
    {
        return $this->exportService->exportPivotExcel();
    }

    /**
     * Ekspor data organisasi lengkap ke Excel/CSV dengan filter opsional.
     * Query param: format (xlsx|csv), industry_id, tier, status
     */
    public function exportExcel(Request $request)
    {
        return $this->exportService->exportExcel();
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
            'industry_id' => 'nullable|exists:industries,id',
        ]);

        try {
            $result = $this->importService->import(
                $request->file('file'),
                $request->user(),
                $request->input('industry_id')
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak dapat diproses: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "{$result['imported']} data diproses: {$result['created']} baru, {$result['updated']} diperbarui, {$result['skipped']} tidak berubah",
            ...$result,
        ]);
    }

    /**
     * Unduh template spreadsheet untuk import organisasi.
     */
    public function downloadTemplate()
    {
        return $this->exportService->downloadImportTemplate();
    }
}

