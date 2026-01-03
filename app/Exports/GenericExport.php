<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $headings;
    
    public function __construct($data, $headings = [])
    {
        $this->data = $data;
        $this->headings = $headings;
    }
    
    public function collection()
    {
        return collect($this->data);
    }
    
    public function headings(): array
    {
        return $this->headings;
    }
    
    public function map($item): array
    {
        // Convert object to array
        if (is_object($item)) {
            $item = $item->toArray();
        }
        
        // Map array values
        return array_values($item);
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}