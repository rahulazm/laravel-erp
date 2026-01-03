<?php

namespace App\Exports;

use App\Models\Bom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BomDetailsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $bom;
    protected $components;
    
    public function __construct(Bom $bom, $components)
    {
        $this->bom = $bom;
        $this->components = $components;
    }
    
    public function collection()
    {
        return collect($this->components);
    }
    
    public function headings(): array
    {
        return [
            'Level',
            'Type',
            'Code',
            'Name',
            'Description',
            'Quantity',
            'Unit of Measure',
            'Unit Cost',
            'Total Cost',
            'Notes'
        ];
    }
    
    public function map($component): array
    {
        return [
            str_repeat('  ', $component['level']) . ($component['level'] > 0 ? '└─ ' : ''),
            ucfirst($component['type']),
            $component['code'],
            $component['name'],
            $component['description'] ?? '',
            $component['quantity'],
            $component['unit_of_measure'],
            $component['unit_cost'],
            $component['total_cost'],
            $component['notes'] ?? ''
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(30);
        
        // Format cost columns
        $sheet->getStyle('H2:H' . (count($this->components) + 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $sheet->getStyle('I2:I' . (count($this->components) + 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');
        
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
    
    public function title(): string
    {
        return 'BOM Components';
    }
}