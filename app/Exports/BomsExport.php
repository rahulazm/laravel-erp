<?php

namespace App\Exports;

use App\Models\Bom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BomsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $boms;
    
    public function __construct($boms)
    {
        $this->boms = $boms;
    }
    
    public function collection()
    {
        return $this->boms;
    }
    
    public function headings(): array
    {
        return [
            'BOM Number',
            'Name',
            'Description',
            'Assembly',
            'Sales Order',
            'Status',
            'Version',
            'Created By',
            'Created Date',
            'Approved By',
            'Approved Date'
        ];
    }
    
    public function map($bom): array
    {
        return [
            $bom->bom_number,
            $bom->name,
            $bom->description,
            $bom->assembly->name ?? 'N/A',
            $bom->salesOrder->order_number ?? 'N/A',
            ucfirst($bom->status),
            $bom->version,
            $bom->createdBy->name ?? 'N/A',
            $bom->created_at->format('Y-m-d H:i'),
            $bom->approvedBy->name ?? 'N/A',
            $bom->approved_at ? $bom->approved_at->format('Y-m-d H:i') : 'N/A'
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}