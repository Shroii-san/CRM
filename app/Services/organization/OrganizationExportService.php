<?php

namespace App\Services\organization;

use App\Exports\OrganizationPivotExport;
use App\Models\Industry;
use App\Models\Organization;
use App\Models\OrganizationContact;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
        if (!file_exists($inotalLogoPath)) {
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
        if (!file_exists($inotalLogoPath)) {
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
        $tierLabel = $organizations->count() . ' Rumah Sakit Terpilih';

        $inotalLogoPath = public_path('img/LOGO INOTAL UNTUK BG TERANG.png');
        if (!file_exists($inotalLogoPath)) {
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

        $data = [];
        $data[] = array_merge(['Keterangan'], $tiers, ['Grand Total']);

        $rowTotal = ['Jumlah Perusahaan'];
        $rowActive = ['Active'];
        $rowInactive = ['Inactive'];
        $rowPic = ['Total PIC'];

        $grandTotal = 0;
        $grandActive = 0;
        $grandInactive = 0;
        $grandPic = 0;

        foreach ($tiers as $tier) {
            $orgIds = Organization::where('tier', $tier)->pluck('id');

            $total = $orgIds->count();
            $active = Organization::where('tier', $tier)->where('is_active', true)->count();
            $inactive = Organization::where('tier', $tier)->where('is_active', false)->count();
            $pic = OrganizationContact::whereIn('organization_id', $orgIds)->count();

            $rowTotal[] = $total;
            $rowActive[] = $active;
            $rowInactive[] = $inactive;
            $rowPic[] = $pic;

            $grandTotal += $total;
            $grandActive += $active;
            $grandInactive += $inactive;
            $grandPic += $pic;
        }

        $rowTotal[] = $grandTotal;
        $rowActive[] = $grandActive;
        $rowInactive[] = $grandInactive;
        $rowPic[] = $grandPic;

        $data[] = $rowTotal;
        $data[] = $rowActive;
        $data[] = $rowInactive;
        $data[] = $rowPic;

        return Excel::download(
            new OrganizationPivotExport($data),
            'Pivot-Organization-' . now()->format('Ymd') . '.xlsx'
        );
    }

    public function exportExcel()
    {
        $format = strtolower((string) request('format', 'xlsx'));

        if (!in_array($format, ['csv', 'xlsx'], true)) {
            abort(400, 'Format export tidak valid. Gunakan CSV atau XLSX.');
        }

        $query = $this->buildOrganizationExportQuery();

        if ($format === 'csv') {
            return $this->exportCsvStream($query);
        }

        return $this->exportXlsxFile($query);
    }

    private function buildOrganizationExportQuery()
    {
        $query = Organization::query()->with([
            'industry',
            'province',
            'regency',
            'district',
            'village',
        ]);

        if (request()->filled('industry_id')) {
            $query->where('industry_id', (int) request('industry_id'));
        }

        if (request()->filled('tier')) {
            $query->where('tier', strtoupper((string) request('tier')));
        }

        if (request()->filled('status')) {
            $status = strtolower((string) request('status'));
            $query->where('is_active', $status === 'active' ? 1 : 0);
        }

        return $query->orderBy('name');
    }

    private function exportCsvStream($query)
    {
        $headers = [
            'Nama Organisasi',
            'Kode Organisasi',
            'Industri',
            'Tier',
            'Deskripsi',
            'Status',
            'Alamat',
            'Provinsi',
            'Kabupaten/Kota',
            'Kecamatan',
            'Kelurahan',
            'Telepon',
            'Email',
            'Website',
            'Nomor SP',
            'Link Kemkes',
            'Latitude',
            'Longitude',
            'Aktif',
        ];

        $filename = 'organizations-export-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            $query->chunkById(500, function ($organizations) use ($handle) {
                foreach ($organizations as $organization) {
                    fputcsv($handle, [
                        $organization->name,
                        $organization->organization_code,
                        $organization->industry?->name ?? '',
                        $organization->tier,
                        $organization->description,
                        $organization->is_active ? 'Active' : 'Inactive',
                        $organization->address,
                        $organization->province?->name ?? '',
                        $organization->regency?->name ?? '',
                        $organization->district?->name ?? '',
                        $organization->village?->name ?? '',
                        $organization->phone,
                        $organization->email,
                        $organization->website,
                        $organization->sp_number,
                        $organization->kemkes_link,
                        $organization->latitude,
                        $organization->longitude,
                        $organization->is_active ? 'Ya' : 'Tidak',
                    ]);
                }
            }, 'id');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    private function exportXlsxFile($query)
    {
        $headers = [
            'Nama Organisasi',
            'Kode Organisasi',
            'Industri',
            'Tier',
            'Deskripsi',
            'Status',
            'Alamat',
            'Provinsi',
            'Kabupaten/Kota',
            'Kecamatan',
            'Kelurahan',
            'Telepon',
            'Email',
            'Website',
            'Nomor SP',
            'Link Kemkes',
            'Latitude',
            'Longitude',
            'Aktif',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        $query->chunkById(500, function ($organizations) use ($sheet, &$row) {
            foreach ($organizations as $organization) {
                $sheet->fromArray([
                    $organization->name,
                    $organization->organization_code,
                    $organization->industry?->name ?? '',
                    $organization->tier,
                    $organization->description,
                    $organization->is_active ? 'Active' : 'Inactive',
                    $organization->address,
                    $organization->province?->name ?? '',
                    $organization->regency?->name ?? '',
                    $organization->district?->name ?? '',
                    $organization->village?->name ?? '',
                    $organization->phone,
                    $organization->email,
                    $organization->website,
                    $organization->sp_number,
                    $organization->kemkes_link,
                    $organization->latitude,
                    $organization->longitude,
                    $organization->is_active ? 'Ya' : 'Tidak',
                ], null, 'A' . $row);

                $row++;
            }
        }, 'id');

        $filename = 'organizations-export-' . now()->format('Y-m-d-His') . '.xlsx';
        $tempPath = storage_path('app/' . $filename);

        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Mengunduh template spreadsheet kosong untuk keperluan import organisasi.
     */
    public function downloadImportTemplate()
    {
        $headers = [
            'Nama Organisasi',
            'Industri',
            'Tier',
            'Deskripsi',
            'Status',
            'Alamat',
            'Provinsi',
            'Kabupaten/Kota',
            'Kecamatan',
            'Kelurahan',
            'Telepon',
            'Email',
            'Website',
            'Nomor SP',
            'Link Kemkes',
            'PIC',
            'Jabatan PIC',
            'Telepon PIC',
            'Email PIC',
        ];

        $sampleRow = [
            'RS Harapan Bersama',
            'Rumah Sakit',
            'A',
            'Rumah sakit umum tipe A di Bandung',
            'Active',
            'Jl. Merdeka No. 10',
            'Jawa Barat',
            'Kota Bandung',
            'Coblong',
            'Dago',
            '0221234567',
            'info@rsharapan.co.id',
            'https://rsharapan.co.id',
            'SP-2026-001',
            'https://sirs.kemkes.go.id/fo/rs/12345',
            'Dr. Budi Santoso',
            'Direktur Utama',
            '081234567890',
            'budi@rsharapan.co.id',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($sampleRow, null, 'A2');

        // Styling header sederhana
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template-import-organisasi.xlsx';
        $tempPath = storage_path('app/' . $filename);

        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}

