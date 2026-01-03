<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Bom;
use App\Models\Item;
use App\Models\ValveCategory;
use App\Models\Assembly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }
    
    public function export(Request $request)
    {
        $exportTypes = [
            'sales_orders' => 'Sales Orders',
            'boms' => 'BOMs',
            'items' => 'Items',
            'valve_categories' => 'Valve Categories',
            'assemblies' => 'Assemblies',
            'checkouts' => 'Checkout History',
            'approvals' => 'Approval History'
        ];
        
        $formats = ['xlsx', 'csv', 'pdf'];
        
        return view('reports.export', compact('exportTypes', 'formats'));
    }
    
    public function exportData(Request $request)
    {
        $request->validate([
            'export_type' => 'required|in:sales_orders,boms,items,valve_categories,assemblies,checkouts,approvals',
            'format' => 'required|in:xlsx,csv,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'filters' => 'nullable|array'
        ]);
        
        $data = $this->getExportData($request);
        
        if ($request->format === 'pdf') {
            return $this->exportToPdf($request->export_type, $data);
        }
        
        return $this->exportToExcel($request->export_type, $data, $request->format);
    }
    
    public function approvalTimes(Request $request)
    {
        $request->validate([
            'period' => 'sometimes|in:week,month,quarter,year,custom',
            'start_date' => 'nullable|required_if:period,custom|date',
            'end_date' => 'nullable|required_if:period,custom|date|after_or_equal:start_date'
        ]);
        
        $dateRange = $this->getDateRange($request);
        
        // Get BOM approval statistics
        $approvalStats = Bom::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_approval_time')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('approved_at')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        
        // Get approval times by approver
        $approverStats = Bom::select(
                'approved_by',
                DB::raw('COUNT(*) as total_approved'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_time'),
                DB::raw('MIN(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as min_time'),
                DB::raw('MAX(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as max_time')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'approved')
            ->whereNotNull('approved_by')
            ->groupBy('approved_by')
            ->with('approvedBy')
            ->get();
        
        // Get pending approvals
        $pendingApprovals = Bom::where('status', 'pending_approval')
            ->where('created_at', '>=', $dateRange['start'])
            ->with(['createdBy', 'assembly'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('reports.approval-times', compact(
            'approvalStats',
            'approverStats',
            'pendingApprovals',
            'dateRange'
        ));
    }
    
    public function creationTrends(Request $request)
    {
        $request->validate([
            'period' => 'sometimes|in:week,month,quarter,year,custom',
            'start_date' => 'nullable|required_if:period,custom|date',
            'end_date' => 'nullable|required_if:period,custom|date|after_or_equal:start_date'
        ]);
        
        $dateRange = $this->getDateRange($request);
        
        // Get creation trends for different entities
        $trends = [];
        
        // Sales Orders trend
        $trends['sales_orders'] = SalesOrder::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        
        // BOMs trend
        $trends['boms'] = Bom::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('is_current', true)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        
        // Items trend
        $trends['items'] = Item::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('is_current', true)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        
        // Valve types distribution
        $valveTypeDistribution = SalesOrder::select(
                'valve_type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('valve_type')
            ->groupBy('valve_type')
            ->orderBy('count', 'desc')
            ->get();
        
        // Item types distribution
        $itemTypeDistribution = Item::select(
                'type',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('is_current', true)
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();
        
        return view('reports.creation-trends', compact(
            'trends',
            'valveTypeDistribution',
            'itemTypeDistribution',
            'dateRange'
        ));
    }
    
    public function obsoleteReport(Request $request)
    {
        $request->validate([
            'period' => 'sometimes|in:week,month,quarter,year,custom',
            'start_date' => 'nullable|required_if:period,custom|date',
            'end_date' => 'nullable|required_if:period,custom|date|after_or_equal:start_date'
        ]);
        
        $dateRange = $this->getDateRange($request);
        
        // Get obsolete items
        $obsoleteItems = Item::where('is_obsolete', true)
            ->whereBetween('updated_at', [$dateRange['start'], $dateRange['end']])
            ->with(['createdBy'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Get obsolete items trend
        $obsoleteTrend = Item::select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('is_obsolete', true)
            ->whereBetween('updated_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->orderBy('date')
            ->get();
        
        // Get reasons for obsoletion
        $obsoleteReasons = DB::table('notes')
            ->select('content', DB::raw('COUNT(*) as count'))
            ->where('type', 'obsolete')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('content')
            ->orderBy('count', 'desc')
            ->get();
        
        return view('reports.obsolete', compact(
            'obsoleteItems',
            'obsoleteTrend',
            'obsoleteReasons',
            'dateRange'
        ));
    }
    
    public function checkoutReport(Request $request)
    {
        $request->validate([
            'period' => 'sometimes|in:week,month,quarter,year,custom',
            'start_date' => 'nullable|required_if:period,custom|date',
            'end_date' => 'nullable|required_if:period,custom|date|after_or_equal:start_date',
            'user_id' => 'nullable|exists:users,id'
        ]);
        
        $dateRange = $this->getDateRange($request);
        
        $query = \App\Models\Checkout::with(['user', 'checkoutable'])
            ->whereBetween('checked_out_at', [$dateRange['start'], $dateRange['end']]);
        
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        $checkouts = $query->orderBy('checked_out_at', 'desc')->paginate(25);
        
        // Get checkout statistics
        $stats = [
            'total_checkouts' => $query->count(),
            'active_checkouts' => $query->whereNull('checked_in_at')->count(),
            'avg_checkout_duration' => $query->whereNotNull('checked_in_at')
                ->avg(DB::raw('TIMESTAMPDIFF(HOUR, checked_out_at, checked_in_at)')),
            'by_type' => $query->select('checkoutable_type', DB::raw('COUNT(*) as count'))
                ->groupBy('checkoutable_type')
                ->get()
        ];
        
        // Get top users by checkout count
        $topUsers = \App\Models\Checkout::select('user_id', DB::raw('COUNT(*) as checkout_count'))
            ->whereBetween('checked_out_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('user_id')
            ->with('user')
            ->orderBy('checkout_count', 'desc')
            ->limit(10)
            ->get();
        
        return view('reports.checkouts', compact(
            'checkouts',
            'stats',
            'topUsers',
            'dateRange'
        ));
    }
    
    private function getExportData($request)
    {
        $query = null;
        
        switch ($request->export_type) {
            case 'sales_orders':
                $query = SalesOrder::with(['valveCategory', 'createdBy']);
                break;
            case 'boms':
                $query = Bom::with(['assembly', 'salesOrder', 'createdBy', 'approvedBy'])
                    ->where('is_current', true);
                break;
            case 'items':
                $query = Item::with(['createdBy'])->where('is_current', true);
                break;
            case 'valve_categories':
                $query = ValveCategory::withCount('salesOrders');
                break;
            case 'assemblies':
                $query = Assembly::with(['parent', 'createdBy']);
                break;
            case 'checkouts':
                $query = \App\Models\Checkout::with(['user', 'checkoutable']);
                break;
            case 'approvals':
                $query = Bom::with(['createdBy', 'approvedBy'])
                    ->whereNotNull('approved_at');
                break;
        }
        
        // Apply date filters
        if ($request->start_date && $query) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->end_date && $query) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Apply additional filters
        if ($request->filters && $query) {
            foreach ($request->filters as $field => $value) {
                if ($value) {
                    $query->where($field, $value);
                }
            }
        }
        
        return $query ? $query->orderBy('created_at', 'desc')->get() : collect();
    }
    
    private function exportToExcel($type, $data, $format)
    {
        $className = 'App\\Exports\\' . ucfirst(camel_case($type)) . 'Export';
        
        if (class_exists($className)) {
            return (new $className($data))->download($type . '-' . date('Y-m-d') . '.' . $format);
        }
        
        // Default export if specific export class doesn't exist
        return Excel::download(new \App\Exports\GenericExport($data, $type), $type . '-' . date('Y-m-d') . '.' . $format);
    }
    
    private function exportToPdf($type, $data)
    {
        $view = 'reports.export.' . str_replace('_', '-', $type) . '-pdf';
        
        if (view()->exists($view)) {
            $pdf = \PDF::loadView($view, compact('data'));
            return $pdf->download($type . '-' . date('Y-m-d') . '.pdf');
        }
        
        // Default PDF view
        $pdf = \PDF::loadView('reports.export.generic-pdf', compact('data', 'type'));
        return $pdf->download($type . '-' . date('Y-m-d') . '.pdf');
    }
    
    private function getDateRange($request)
    {
        $now = Carbon::now();
        $period = $request->get('period', 'month');
        
        switch ($period) {
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            case 'quarter':
                return [
                    'start' => $now->copy()->startOfQuarter(),
                    'end' => $now->copy()->endOfQuarter()
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
            case 'custom':
                return [
                    'start' => Carbon::parse($request->start_date)->startOfDay(),
                    'end' => Carbon::parse($request->end_date)->endOfDay()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }
}