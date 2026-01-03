<?php

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\Bom;
use App\Models\Item;
use App\Models\ValveCategory;
use App\Models\Assembly;
use App\Models\Checkout;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportService
{
    /**
     * Export data based on type and format
     */
    public function export($type, $format, $filters = [], $columns = [])
    {
        $data = $this->getData($type, $filters);
        
        switch ($format) {
            case 'xlsx':
            case 'csv':
                return $this->exportToExcel($type, $data, $format, $columns);
            case 'pdf':
                return $this->exportToPdf($type, $data, $columns);
            default:
                throw new \Exception('Unsupported export format');
        }
    }

    /**
     * Get data based on type and filters
     */
    private function getData($type, $filters)
    {
        switch ($type) {
            case 'sales_orders':
                return $this->getSalesOrdersData($filters);
            case 'boms':
                return $this->getBomsData($filters);
            case 'items':
                return $this->getItemsData($filters);
            case 'valve_categories':
                return $this->getValveCategoriesData($filters);
            case 'assemblies':
                return $this->getAssembliesData($filters);
            case 'checkouts':
                return $this->getCheckoutsData($filters);
            case 'tasks':
                return $this->getTasksData($filters);
            case 'approval_history':
                return $this->getApprovalHistoryData($filters);
            case 'obsolete_items':
                return $this->getObsoleteItemsData($filters);
            case 'bom_versions':
                return $this->getBomVersionsData($filters);
            case 'item_versions':
                return $this->getItemVersionsData($filters);
            default:
                throw new \Exception('Unsupported export type');
        }
    }

    /**
     * Export data to Excel/CSV format
     */
    private function exportToExcel($type, $data, $format, $columns = [])
    {
        $exportClass = $this->getExportClass($type);
        
        if (class_exists($exportClass)) {
            $export = new $exportClass($data, $columns);
            $filename = $this->generateFilename($type, $format);
            
            return Excel::download($export, $filename);
        }
        
        // Default export for unsupported types
        return Excel::download(new GenericExport($data, $this->getHeadings($type)), 
            $this->generateFilename($type, $format));
    }

    /**
     * Export data to PDF format
     */
    private function exportToPdf($type, $data, $columns = [])
    {
        $view = $this->getPdfView($type);
        
        if (!view()->exists($view)) {
            $view = 'exports.generic-pdf';
        }
        
        $pdf = Pdf::loadView($view, [
            'data' => $data,
            'type' => $type,
            'columns' => $columns,
            'export_date' => now()->format('Y-m-d H:i:s'),
            'total_records' => count($data)
        ]);
        
        $filename = $this->generateFilename($type, 'pdf');
        
        return $pdf->download($filename);
    }

    /**
     * Get sales orders data with filters
     */
    private function getSalesOrdersData($filters)
    {
        $query = SalesOrder::with(['valveCategory', 'createdBy', 'boms'])
            ->select([
                'order_number',
                'customer_name',
                'customer_email',
                'customer_phone',
                'valve_type',
                'valve_category_id',
                'total_amount',
                'status',
                'delivery_date',
                'created_at',
                'created_by',
                'notes'
            ]);
        
        $this->applyFilters($query, $filters);
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get BOMs data with filters
     */
    private function getBomsData($filters)
    {
        $query = Bom::with(['assembly', 'salesOrder', 'createdBy', 'approvedBy'])
            ->where('is_current', true)
            ->select([
                'bom_number',
                'name',
                'description',
                'assembly_id',
                'sales_order_id',
                'status',
                'version',
                'created_at',
                'created_by',
                'approved_at',
                'approved_by'
            ]);
        
        $this->applyFilters($query, $filters);
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get items data with filters
     */
    private function getItemsData($filters)
    {
        $query = Item::with(['createdBy'])
            ->where('is_current', true)
            ->select([
                'item_code',
                'name',
                'description',
                'type',
                'unit_of_measure',
                'unit_cost',
                'weight',
                'material_grade',
                'version',
                'is_obsolete',
                'created_at',
                'created_by'
            ]);
        
        $this->applyFilters($query, $filters);
        
        return $query->orderBy('item_code')->get();
    }

    /**
     * Get valve categories data with filters
     */
    private function getValveCategoriesData($filters)
    {
        $query = ValveCategory::withCount('salesOrders')
            ->select([
                'code',
                'name',
                'description',
                'pressure_rating',
                'temperature_rating',
                'material',
                'end_connection',
                'standard',
                'is_active',
                'created_at'
            ]);
        
        $this->applyFilters($query, $filters);
        
        return $query->orderBy('code')->get();
    }

    /**
     * Get assemblies data with filters
     */
    private function getAssembliesData($filters)
    {
        $query = Assembly::with(['parent', 'createdBy', 'items'])
            ->select([
                'code',
                'name',
                'description',
                'type',
                'parent_id',
                'drawing_number',
                'revision',
                'weight',
                'material',
                'is_active',
                'created_at',
                'created_by'
            ]);
        
        $this->applyFilters($query, $filters);
        
        // Calculate total cost for each assembly
        $assemblies = $query->orderBy('code')->get();
        
        return $assemblies->map(function($assembly) {
            $assembly->total_cost = $assembly->total_cost;
            $assembly->component_count = $assembly->component_count;
            return $assembly;
        });
    }

    /**
     * Get checkouts data with filters
     */
    private function getCheckoutsData($filters)
    {
        $query = Checkout::with(['user', 'checkoutable'])
            ->select([
                'checkoutable_type',
                'checkoutable_id',
                'user_id',
                'checked_out_at',
                'checked_in_at',
                'purpose',
                'created_at'
            ]);
        
        $this->applyFilters($query, $filters);
        
        return $query->orderBy('checked_out_at', 'desc')->get();
    }

    /**
     * Get tasks data with filters
     */
    private function getTasksData($filters)
    {
        $query = Task::with(['assignedTo', 'createdBy', 'completedBy'])
            ->select([
                'title',
                'description',
                'type',
                'priority',
                'status',
                'assigned_to',
                'due_date',
                'completed_at',
                'estimated_hours',
                'actual_hours',
                'related_type',
                'related_id',
                'created_at',
                'created_by'
            ]);
        
        $this->applyFilters($query, $filters);
        
        return $query->orderBy('due_date')->get();
    }

    /**
     * Get approval history data
     */
    private function getApprovalHistoryData($filters)
    {
        $query = Bom::with(['createdBy', 'approvedBy', 'assembly'])
            ->whereNotNull('approved_at')
            ->select([
                'bom_number',
                'name',
                'version',
                'status',
                'created_at',
                'created_by',
                'approved_at',
                'approved_by',
                DB::raw('TIMESTAMPDIFF(HOUR, created_at, approved_at) as approval_hours')
            ]);
        
        $this->applyFilters($query, $filters);
        
        return $query->orderBy('approved_at', 'desc')->get();
    }

    /**
     * Get obsolete items data
     */
    private function getObsoleteItemsData($filters)
    {
        $query = Item::with(['createdBy'])
            ->where('is_obsolete', true)
            ->where('is_current', true)
            ->select([
                'item_code',
                'name',
                'description',
                'type',
                'unit_of_measure',
                'unit_cost',
                'material_grade',
                'version',
                'created_at',
                'updated_at as obsolete_date',
                'created_by'
            ]);
        
        $this->applyFilters($query, $filters);
        
        return $query->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Get BOM versions data
     */
    private function getBomVersionsData($filters)
    {
        $query = \App\Models\BomVersion::with(['bom', 'createdBy'])
            ->select([
                'bom_id',
                'version',
                'changes_description',
                'created_at',
                'created_by'
            ]);
        
        $this->applyFilters($query, $filters);
        
        if (isset($filters['bom_id'])) {
            $query->where('bom_id', $filters['bom_id']);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get item versions data
     */
    private function getItemVersionsData($filters)
    {
        $query = \App\Models\ItemVersion::with(['item', 'createdBy'])
            ->select([
                'item_id',
                'version',
                'changes_description',
                'created_at',
                'created_by'
            ]);
        
        $this->applyFilters($query, $filters);
        
        if (isset($filters['item_id'])) {
            $query->where('item_id', $filters['item_id']);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, $filters)
    {
        foreach ($filters as $field => $value) {
            if (empty($value)) {
                continue;
            }
            
            switch ($field) {
                case 'date_from':
                    $query->whereDate('created_at', '>=', $value);
                    break;
                case 'date_to':
                    $query->whereDate('created_at', '<=', $value);
                    break;
                case 'status':
                    $query->where('status', $value);
                    break;
                case 'type':
                    $query->where('type', $value);
                    break;
                case 'created_by':
                    $query->where('created_by', $value);
                    break;
                case 'valve_category_id':
                    $query->where('valve_category_id', $value);
                    break;
                case 'is_obsolete':
                    $query->where('is_obsolete', $value === 'true');
                    break;
                case 'is_active':
                    $query->where('is_active', $value === 'true');
                    break;
                case 'search':
                    $this->applySearchFilter($query, $value);
                    break;
                default:
                    // For custom fields
                    if (in_array($field, $query->getModel()->getFillable())) {
                        $query->where($field, $value);
                    }
                    break;
            }
        }
    }

    /**
     * Apply search filter to query
     */
    private function applySearchFilter($query, $searchTerm)
    {
        $model = $query->getModel();
        $table = $model->getTable();
        $searchableFields = $this->getSearchableFields(get_class($model));
        
        $query->where(function($q) use ($searchTerm, $searchableFields, $table) {
            foreach ($searchableFields as $field) {
                $q->orWhere("{$table}.{$field}", 'like', "%{$searchTerm}%");
            }
        });
    }

    /**
     * Get searchable fields for each model
     */
    private function getSearchableFields($modelClass)
    {
        $searchableFields = [
            SalesOrder::class => ['order_number', 'customer_name', 'customer_email', 'valve_type'],
            Bom::class => ['bom_number', 'name', 'description'],
            Item::class => ['item_code', 'name', 'description', 'material_grade'],
            ValveCategory::class => ['code', 'name', 'material', 'standard'],
            Assembly::class => ['code', 'name', 'description', 'drawing_number'],
            Task::class => ['title', 'description'],
        ];
        
        return $searchableFields[$modelClass] ?? ['name'];
    }

    /**
     * Get export class for specific type
     */
    private function getExportClass($type)
    {
        $exportClasses = [
            'sales_orders' => \App\Exports\SalesOrdersExport::class,
            'boms' => \App\Exports\BomsExport::class,
            'items' => \App\Exports\ItemsExport::class,
            'valve_categories' => \App\Exports\ValveCategoriesExport::class,
            'assemblies' => \App\Exports\AssembliesExport::class,
            'checkouts' => \App\Exports\CheckoutsExport::class,
            'tasks' => \App\Exports\TasksExport::class,
            'approval_history' => \App\Exports\ApprovalHistoryExport::class,
            'obsolete_items' => \App\Exports\ObsoleteItemsExport::class,
        ];
        
        return $exportClasses[$type] ?? null;
    }

    /**
     * Get PDF view for specific type
     */
    private function getPdfView($type)
    {
        return "exports.{$type}.pdf";
    }

    /**
     * Get default headings for export type
     */
    private function getHeadings($type)
    {
        $headings = [
            'sales_orders' => [
                'Order Number',
                'Customer Name',
                'Customer Email',
                'Customer Phone',
                'Valve Type',
                'Valve Category',
                'Total Amount',
                'Status',
                'Delivery Date',
                'Created Date',
                'Created By',
                'Notes'
            ],
            'boms' => [
                'BOM Number',
                'Name',
                'Description',
                'Assembly',
                'Sales Order',
                'Status',
                'Version',
                'Created Date',
                'Created By',
                'Approved Date',
                'Approved By'
            ],
            'items' => [
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
                'Created Date',
                'Created By'
            ],
            'valve_categories' => [
                'Code',
                'Name',
                'Description',
                'Pressure Rating',
                'Temperature Rating',
                'Material',
                'End Connection',
                'Standard',
                'Active',
                'Created Date'
            ],
            'assemblies' => [
                'Code',
                'Name',
                'Description',
                'Type',
                'Parent Assembly',
                'Drawing Number',
                'Revision',
                'Weight',
                'Material',
                'Total Cost',
                'Component Count',
                'Active',
                'Created Date'
            ],
            'checkouts' => [
                'Type',
                'Item/BOM',
                'User',
                'Checked Out',
                'Checked In',
                'Duration (Hours)',
                'Purpose',
                'Created Date'
            ],
            'tasks' => [
                'Title',
                'Description',
                'Type',
                'Priority',
                'Status',
                'Assigned To',
                'Due Date',
                'Completed Date',
                'Estimated Hours',
                'Actual Hours',
                'Created Date',
                'Created By'
            ],
        ];
        
        return $headings[$type] ?? [];
    }

    /**
     * Generate filename for export
     */
    private function generateFilename($type, $format)
    {
        $timestamp = date('Y-m-d_H-i-s');
        $typeName = str_replace('_', '-', $type);
        
        return "{$typeName}-export-{$timestamp}.{$format}";
    }

    /**
     * Export dashboard statistics
     */
    public function exportDashboardStats($period = 'month')
    {
        $dateRange = $this->getDateRange($period);
        
        $stats = [
            'period' => $period,
            'date_range' => $dateRange,
            'sales_orders' => $this->getSalesOrderStats($dateRange),
            'boms' => $this->getBomStats($dateRange),
            'items' => $this->getItemStats($dateRange),
            'approvals' => $this->getApprovalStats($dateRange),
            'checkouts' => $this->getCheckoutStats($dateRange)
        ];
        
        $pdf = Pdf::loadView('exports.dashboard-stats-pdf', [
            'stats' => $stats,
            'export_date' => now()->format('Y-m-d H:i:s')
        ]);
        
        return $pdf->download("dashboard-stats-{$period}-" . date('Y-m-d') . '.pdf');
    }

    /**
     * Get date range based on period
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'today':
                return [
                    'start' => $now->startOfDay(),
                    'end' => $now->endOfDay()
                ];
            case 'week':
                return [
                    'start' => $now->startOfWeek(),
                    'end' => $now->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => $now->startOfMonth(),
                    'end' => $now->endOfMonth()
                ];
            case 'quarter':
                return [
                    'start' => $now->startOfQuarter(),
                    'end' => $now->endOfQuarter()
                ];
            case 'year':
                return [
                    'start' => $now->startOfYear(),
                    'end' => $now->endOfYear()
                ];
            default:
                return [
                    'start' => $now->startOfMonth(),
                    'end' => $now->endOfMonth()
                ];
        }
    }

    /**
     * Get sales order statistics
     */
    private function getSalesOrderStats($dateRange)
    {
        return [
            'total' => SalesOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            'total_amount' => SalesOrder::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->sum('total_amount'),
            'by_status' => SalesOrder::select('status', DB::raw('COUNT(*) as count'))
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->groupBy('status')
                ->get(),
            'by_valve_type' => SalesOrder::select('valve_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as amount'))
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->whereNotNull('valve_type')
                ->groupBy('valve_type')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
        ];
    }

    /**
     * Get BOM statistics
     */
    private function getBomStats($dateRange)
    {
        return [
            'total' => Bom::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('is_current', true)
                ->count(),
            'by_status' => Bom::select('status', DB::raw('COUNT(*) as count'))
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('is_current', true)
                ->groupBy('status')
                ->get(),
            'approval_times' => Bom::select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours'))
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->whereNotNull('approved_at')
                ->first()
        ];
    }

    /**
     * Get item statistics
     */
    private function getItemStats($dateRange)
    {
        return [
            'total' => Item::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('is_current', true)
                ->count(),
            'by_type' => Item::select('type', DB::raw('COUNT(*) as count'))
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('is_current', true)
                ->groupBy('type')
                ->get(),
            'obsolete_count' => Item::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('is_current', true)
                ->where('is_obsolete', true)
                ->count()
        ];
    }

    /**
     * Get approval statistics
     */
    private function getApprovalStats($dateRange)
    {
        return [
            'total_approvals' => Bom::whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
                ->where('status', 'approved')
                ->count(),
            'total_rejections' => Bom::whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
                ->where('status', 'rejected')
                ->count(),
            'by_approver' => Bom::select('approved_by', DB::raw('COUNT(*) as count'))
                ->whereBetween('approved_at', [$dateRange['start'], $dateRange['end']])
                ->whereNotNull('approved_by')
                ->groupBy('approved_by')
                ->with('approvedBy')
                ->get()
        ];
    }

    /**
     * Get checkout statistics
     */
    private function getCheckoutStats($dateRange)
    {
        return [
            'total_checkouts' => Checkout::whereBetween('checked_out_at', [$dateRange['start'], $dateRange['end']])->count(),
            'active_checkouts' => Checkout::whereBetween('checked_out_at', [$dateRange['start'], $dateRange['end']])
                ->whereNull('checked_in_at')
                ->count(),
            'by_type' => Checkout::select('checkoutable_type', DB::raw('COUNT(*) as count'))
                ->whereBetween('checked_out_at', [$dateRange['start'], $dateRange['end']])
                ->groupBy('checkoutable_type')
                ->get(),
            'by_user' => Checkout::select('user_id', DB::raw('COUNT(*) as count'))
                ->whereBetween('checked_out_at', [$dateRange['start'], $dateRange['end']])
                ->groupBy('user_id')
                ->with('user')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
        ];
    }

    /**
     * Export BOM details with components
     */
    public function exportBomDetails($bomId, $format = 'pdf')
    {
        $bom = Bom::with(['assembly', 'items', 'createdBy', 'approvedBy'])
            ->findOrFail($bomId);
        
        // Get flattened BOM structure
        $components = [];
        if ($bom->assembly) {
            $components = $bom->assembly->getFlattenedBom();
        }
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.bom-details-pdf', [
                'bom' => $bom,
                'components' => $components,
                'export_date' => now()->format('Y-m-d H:i:s')
            ]);
            
            return $pdf->download("bom-{$bom->bom_number}-details.pdf");
        }
        
        // For Excel export
        $export = new \App\Exports\BomDetailsExport($bom, $components);
        return Excel::download($export, "bom-{$bom->bom_number}-details.xlsx");
    }

    /**
     * Export assembly tree structure
     */
    public function exportAssemblyTree($assemblyId, $format = 'pdf')
    {
        $assembly = Assembly::with(['items', 'subAssemblies'])->findOrFail($assemblyId);
        $tree = $assembly->getTreeStructure();
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.assembly-tree-pdf', [
                'assembly' => $assembly,
                'tree' => $tree,
                'export_date' => now()->format('Y-m-d H:i:s')
            ]);
            
            return $pdf->download("assembly-{$assembly->code}-tree.pdf");
        }
        
        // For Excel export
        $export = new \App\Exports\AssemblyTreeExport($assembly, $tree);
        return Excel::download($export, "assembly-{$assembly->code}-tree.xlsx");
    }

    /**
     * Generate consolidated report
     */
    public function generateConsolidatedReport($startDate, $endDate, $format = 'pdf')
    {
        $data = [
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'summary' => $this->getConsolidatedSummary($startDate, $endDate),
            'sales_orders' => $this->getSalesOrdersData(['date_from' => $startDate, 'date_to' => $endDate]),
            'boms' => $this->getBomsData(['date_from' => $startDate, 'date_to' => $endDate]),
            'items' => $this->getItemsData(['date_from' => $startDate, 'date_to' => $endDate])
        ];
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.consolidated-report-pdf', [
                'data' => $data,
                'export_date' => now()->format('Y-m-d H:i:s')
            ]);
            
            $filename = "consolidated-report-" . date('Y-m-d', strtotime($startDate)) . 
                       "-to-" . date('Y-m-d', strtotime($endDate)) . ".pdf";
            
            return $pdf->download($filename);
        }
        
        // For Excel export
        $export = new \App\Exports\ConsolidatedReportExport($data);
        $filename = "consolidated-report-" . date('Y-m-d', strtotime($startDate)) . 
                   "-to-" . date('Y-m-d', strtotime($endDate)) . ".xlsx";
        
        return Excel::download($export, $filename);
    }

    /**
     * Get consolidated summary
     */
    private function getConsolidatedSummary($startDate, $endDate)
    {
        return [
            'sales_orders_count' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->count(),
            'sales_orders_amount' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'boms_count' => Bom::whereBetween('created_at', [$startDate, $endDate])->where('is_current', true)->count(),
            'items_count' => Item::whereBetween('created_at', [$startDate, $endDate])->where('is_current', true)->count(),
            'new_customers' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])
                ->distinct('customer_email')
                ->count('customer_email'),
            'approval_rate' => $this->calculateApprovalRate($startDate, $endDate)
        ];
    }

    /**
     * Calculate approval rate
     */
    private function calculateApprovalRate($startDate, $endDate)
    {
        $total = Bom::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'rejected'])
            ->count();
        
        $approved = Bom::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved')
            ->count();
        
        return $total > 0 ? round(($approved / $total) * 100, 2) : 0;
    }
}