<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanyExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Company::select(
            'company_name', 
            'company_type_id', 
            'tier', 
            'description', 
            'status'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Nama Perusahaan',
            'Jenis',
            'Tier',
            'Deskripsi',
            'Status'
        ];
    }
}
