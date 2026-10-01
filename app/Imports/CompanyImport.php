<?php

namespace App\Imports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CompanyImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Company([
            'company_name'     => $row['nama_perusahaan'],
            'company_type_id'  => $row['jenis'],
            'tier'             => $row['tier'],
            'description'      => $row['deskripsi'] ?? '-',
            'status'           => $row['status'] ?? 'Active',
        ]);
    }
}
