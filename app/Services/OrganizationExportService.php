<?php

namespace App\Services;

use App\Exports\OrganizationPivotExport;
use App\Models\Industry;
use App\Models\Organization;
use App\Models\OrganizationContact;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class OrganizationExportService
{
    /**
     * Ekspor profil detail satu organisasi ke PDF.
     */
    public function exportPdf(int $id)
    {
        $organization = Organization::with([
            'industry',
            'province',
            'regency',
            'district',
            'village',
            'contacts.person',
            'socialProfiles',
        ])->findOrFail($id);

        // PIC diurutkan berdasarkan nama person
        $contacts = $organization->contacts
            ->sortBy(fn($c) => $c->person?->name ?? '')
            ->values();

        $inotalLogoPath = public_path('img/LOGO INOTAL UNTUK BG TERANG.png');
        if (! file_exists($inotalLogoPath)) {
            $inotalLogoPath = null;
        }

        $documentNumber = 'INOTAL/RS/' . now()->format('Y') . '/' . str_pad($organization->id, 4, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView(
            'pages.organization-pdf',
            compact('organization', 'contacts', 'inotalLogoPath', 'documentNumber')
        );
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Informasi-RS-' . str_replace(' ', '-', $organization->name) . '.pdf');
    }

    /**
     * Ekspor daftar organisasi yang difilter berdasarkan industri dan/atau tier ke PDF.
     */
    public function exportPdfList(?int $industryId = null, ?string $tier = null)
    {
        $query = Organization::with(['industry', 'province', 'regency', 'district', 'village']);

        if ($industryId) {
            $query->where('industry_id', $industryId);
        }

        if ($tier) {
            $query->where('tier', $tier);
        }

        $organizations = $query->orderBy('name')->get();

        $industryLabel = $industryId
            ? (Industry::find($industryId)?->name ?? 'Semua Tipe')
            : 'Semua Tipe';

        $tierLabel = $tier ? 'Tier ' . $tier : 'Semua Tier';

        $inotalLogoPath = public_path('img/LOGO INOTAL UNTUK BG TERANG.png');
        if (! file_exists($inotalLogoPath)) {
            $inotalLogoPath = null;
        }

        $documentNumber = 'INOTAL/RS-LIST/' . now()->format('Y') . '/' . now()->format('mdHis');

        $pdf = Pdf::loadView(
            'pages.organization-pdf-list',
            compact('organizations', 'industryLabel', 'tierLabel', 'inotalLogoPath', 'documentNumber')
        );
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Daftar-Rumah-Sakit-' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Ekspor organisasi yang dipilih secara manual ke PDF.
     *
     * @param array<int|string> $ids
     */
    public function exportPdfSelected(array $ids)
    {
        $organizations = Organization::with(['industry', 'province', 'regency', 'district', 'village'])
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get();

        $industryLabel = 'Pilihan Manual';
        $tierLabel     = $organizations->count() . ' Rumah Sakit Terpilih';

        $inotalLogoPath = public_path('img/LOGO INOTAL UNTUK BG TERANG.png');
        if (! file_exists($inotalLogoPath)) {
            $inotalLogoPath = null;
        }

        $documentNumber = 'INOTAL/RS-LIST/' . now()->format('Y') . '/' . now()->format('mdHis');

        $pdf = Pdf::loadView(
            'pages.organization-pdf-list',
            compact('organizations', 'industryLabel', 'tierLabel', 'inotalLogoPath', 'documentNumber')
        );
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Daftar-RS-Pilihan-' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Ekspor rekap pivot organisasi per tier ke file Excel.
     * Menghitung total, aktif, nonaktif, dan total PIC per tier.
     */
    public function exportPivotExcel()
    {
        $tiers = ['A', 'B', 'C', 'D'];

        $data   = [];
        $data[] = array_merge(['Keterangan'], $tiers, ['Grand Total']);

        $rowTotal    = ['Jumlah Perusahaan'];
        $rowActive   = ['Active'];
        $rowInactive = ['Inactive'];
        $rowPic      = ['Total PIC'];

        $grandTotal    = 0;
        $grandActive   = 0;
        $grandInactive = 0;
        $grandPic      = 0;

        foreach ($tiers as $tier) {
            $orgIds = Organization::where('tier', $tier)->pluck('id');

            $total    = $orgIds->count();
            $active   = Organization::where('tier', $tier)->where('is_active', true)->count();
            $inactive = Organization::where('tier', $tier)->where('is_active', false)->count();
            $pic      = OrganizationContact::whereIn('organization_id', $orgIds)->count();

            $rowTotal[]    = $total;
            $rowActive[]   = $active;
            $rowInactive[] = $inactive;
            $rowPic[]      = $pic;

            $grandTotal    += $total;
            $grandActive   += $active;
            $grandInactive += $inactive;
            $grandPic      += $pic;
        }

        $rowTotal[]    = $grandTotal;
        $rowActive[]   = $grandActive;
        $rowInactive[] = $grandInactive;
        $rowPic[]      = $grandPic;

        $data[] = $rowTotal;
        $data[] = $rowActive;
        $data[] = $rowInactive;
        $data[] = $rowPic;

        return Excel::download(
            new OrganizationPivotExport($data),
            'Pivot-Organization-' . now()->format('Ymd') . '.xlsx'
        );
    }
}
