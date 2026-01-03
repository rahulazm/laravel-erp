<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $items;
    
    public function __construct($items)
    {
        $this->items = $items;
    }
    
    public function collection()
    {
        return $this->items;
    }
    
    public function headings(): array
    {
        return [
            'Item Code',
            'Name',
            'Description',
            'Type',
            'Unit of Measure',
            'Unit Cost',
            'Weight',
            'Material Grade',
            'Version',
            'Obsolete',
            'Created By',
            'Created Date'
        ];
    }
    
    public function map($item): array
    {
        return [
            $item->item_code,
            $item->name,
            $item->description,
            ucfirst(str_replace('_', ' ', $item->type)),
            $item->unit_of_measure,
            $item->unit_cost,
            $item->weight,
            $item->material_grade,
            $item->version,
            $item->is_obsolete ? 'Yes' : 'No',
            $item->createdBy->name ?? 'N/A',
            $item->created_at->format('Y-m-d H:i')
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'F' => [
                'alignment' => ['horizontal' => 'right'],
                'numberFormat' => ['formatCode' => '#,##0.00']
            ],
            'G' => [
                'alignment' => ['horizontal' => 'right'],
                'numberFormat' => ['formatCode' => '#,##0.00']
            ],
        ];
    }
}