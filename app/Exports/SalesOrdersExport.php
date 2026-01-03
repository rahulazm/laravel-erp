<?php

namespace App\Exports;

use App\Models\SalesOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesOrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $salesOrders;
    
    public function __construct($salesOrders)
    {
        $this->salesOrders = $salesOrders;
    }
    
    public function collection()
    {
        return $this->salesOrders;
    }
    
    public function headings(): array
    {
        return [
            'Order Number',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Shipping Address',
            'Valve Type',
            'Valve Category',
            'Total Amount',
            'Status',
            'Delivery Date',
            'Created By',
            'Created Date',
            'Notes'
        ];
    }
    
    public function map($salesOrder): array
    {
        return [
            $salesOrder->order_number,
            $salesOrder->customer_name,
            $salesOrder->customer_email,
            $salesOrder->customer_phone,
            $salesOrder->shipping_address,
            $salesOrder->valve_type,
            $salesOrder->valveCategory->name ?? 'N/A',
            $salesOrder->total_amount,
            ucfirst($salesOrder->status),
            $salesOrder->delivery_date->format('Y-m-d'),
            $salesOrder->createdBy->name ?? 'N/A',
            $salesOrder->created_at->format('Y-m-d H:i'),
            $salesOrder->notes
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'G' => ['alignment' => ['horizontal' => 'right']],
            'H' => [
                'alignment' => ['horizontal' => 'right'],
                'numberFormat' => ['formatCode' => '#,##0.00']
            ],
        ];
    }
}