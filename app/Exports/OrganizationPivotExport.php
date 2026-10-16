<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class OrganizationPivotExport implements FromArray, WithStyles, ShouldAutoSize
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow    = $sheet->getHighestRow();

        // Baris header (baris 1): bold, teks putih, background navy
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1E3A8A');
        $sheet->getStyle('A1:' . $lastColumn . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Kolom "Keterangan" (kolom A): bold
        $sheet->getStyle('A1:A' . $lastRow)->getFont()->setBold(true);

        // Kolom Grand Total (kolom paling kanan): background ungu muda, bold
        $lastColumnIndex  = Coordinate::columnIndexFromString($lastColumn);
        $grandTotalColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);

        $sheet->getStyle($grandTotalColumn . '2:' . $grandTotalColumn . $lastRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E0E7FF');
        $sheet->getStyle($grandTotalColumn . '2:' . $grandTotalColumn . $lastRow)->getFont()->setBold(true);

        return [];
    }
}
