<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\ValveCategory;
use App\Models\Bom;
use App\Models\Item;
use App\Http\Requests\StoreSalesOrderRequest;
use App\Http\Requests\UpdateSalesOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create,sales_order')->only(['create', 'store']);
        $this->middleware('can:update,sales_order')->only(['edit', 'update']);
        $this->middleware('can:delete,sales_order')->only(['destroy']);
    }
    
    public function index(Request $request)
    {
        $query = SalesOrder::with(['valveCategory', 'createdBy', 'boms'])
            ->withCount('boms');
            
        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('valve_category_id') && $request->valve_category_id) {
            $query->where('valve_category_id', $request->valve_category_id);
        }
        
        if ($request->has('valve_type') && $request->valve_type) {
            $query->where('valve_type', 'like', "%{$request->valve_type}%");
        }
        
        if ($request->has('customer_name') && $request->customer_name) {
            $query->where('customer_name', 'like', "%{$request->customer_name}%");
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('delivery_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('delivery_date', '<=', $request->date_to);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('valve_type', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        $salesOrders = $query->paginate(20)->withQueryString();
        $valveCategories = ValveCategory::all();
        
        // Statistics for the view
        $totalOrders = SalesOrder::count();
        $pendingOrders = SalesOrder::whereIn('status', ['draft', 'pending', 'confirmed'])->count();
        $completedOrders = SalesOrder::where('status', 'completed')->count();
        $cancelledOrders = SalesOrder::where('status', 'cancelled')->count();
        
        // Get unique valve types for filter
        $valveTypes = SalesOrder::distinct()->pluck('valve_type')->filter();
        
        return view('sales-orders.index', compact(
            'salesOrders', 
            'valveCategories',
            'valveTypes',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'cancelledOrders'
        ));
    }
    
    public function create()
    {
        $valveCategories = ValveCategory::orderBy('name')->get();
        
        // Generate next order number
        $nextOrderNumber = $this->generateOrderNumber();
        
        // Get unique valve types from existing orders for suggestions
        $existingValveTypes = SalesOrder::distinct()
            ->whereNotNull('valve_type')
            ->pluck('valve_type');
        
        return view('sales-orders.create', compact(
            'valveCategories',
            'nextOrderNumber',
            'existingValveTypes'
        ));
    }
    
    public function store(StoreSalesOrderRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            
            // Calculate total amount if items are provided
            if ($request->has('items')) {
                $totalAmount = 0;
                foreach ($request->items as $item) {
                    $totalAmount += ($item['quantity'] * $item['unit_price']);
                }
                $data['total_amount'] = $totalAmount;
            }
            
            // Create sales order
            $salesOrder = SalesOrder::create($data);
            
            // If create_bom flag is set, create a draft BOM
            if ($request->has('create_bom') && $request->create_bom) {
                $bom = $this->createBomFromSalesOrder($salesOrder);
                
                // If items are provided, add them to BOM
                if ($request->has('items')) {
                    $this->addItemsToBom($bom, $request->items);
                }
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($salesOrder)
                ->log('created sales order');
            
            DB::commit();
            
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('success', 'Sales Order created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create Sales Order: ' . $e->getMessage());
        }
    }
    
    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load([
            'valveCategory', 
            'createdBy', 
            'boms' => function($query) {
                $query->where('is_current', true)
                      ->with(['assembly', 'approvedBy']);
            }
        ]);
        
        // Get related BOMs count by status
        $bomStats = [
            'total' => $salesOrder->boms()->count(),
            'draft' => $salesOrder->boms()->where('status', 'draft')->count(),
            'pending' => $salesOrder->boms()->where('status', 'pending_approval')->count(),
            'approved' => $salesOrder->boms()->where('status', 'approved')->count(),
            'rejected' => $salesOrder->boms()->where('status', 'rejected')->count(),
        ];
        
        // Get recent activities
        $activities = $salesOrder->activities()
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get similar orders for reference
        $similarOrders = SalesOrder::where('valve_type', $salesOrder->valve_type)
            ->where('id', '!=', $salesOrder->id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('sales-orders.show', compact(
            'salesOrder',
            'bomStats',
            'activities',
            'similarOrders'
        ));
    }
    
    public function edit(SalesOrder $salesOrder)
    {
        // Check if sales order can be edited
        if (!$this->canEditSalesOrder($salesOrder)) {
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'This sales order cannot be edited in its current status.');
        }
        
        $valveCategories = ValveCategory::orderBy('name')->get();
        $existingValveTypes = SalesOrder::distinct()
            ->whereNotNull('valve_type')
            ->where('id', '!=', $salesOrder->id)
            ->pluck('valve_type');
        
        return view('sales-orders.edit', compact(
            'salesOrder',
            'valveCategories',
            'existingValveTypes'
        ));
    }
    
    public function update(UpdateSalesOrderRequest $request, SalesOrder $salesOrder)
    {
        // Check if sales order can be edited
        if (!$this->canEditSalesOrder($salesOrder)) {
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'This sales order cannot be edited in its current status.');
        }
        
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Update sales order
            $salesOrder->update($data);
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($salesOrder)
                ->log('updated sales order');
            
            DB::commit();
            
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('success', 'Sales Order updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update Sales Order: ' . $e->getMessage());
        }
    }
    
    public function destroy(SalesOrder $salesOrder)
    {
        // Check if sales order can be deleted
        if ($salesOrder->boms()->exists()) {
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'Cannot delete Sales Order with associated BOMs.');
        }
        
        if (!in_array($salesOrder->status, ['draft', 'cancelled'])) {
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'Only draft or cancelled sales orders can be deleted.');
        }
        
        DB::beginTransaction();
        
        try {
            // Log activity before deletion
            activity()
                ->causedBy(Auth::user())
                ->performedOn($salesOrder)
                ->log('deleted sales order');
            
            $salesOrder->delete();
            
            DB::commit();
            
            return redirect()->route('sales-orders.index')
                ->with('success', 'Sales Order deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'Failed to delete Sales Order: ' . $e->getMessage());
        }
    }
    
    public function updateStatus(Request $request, SalesOrder $salesOrder)
    {
        $request->validate([
            'status' => 'required|in:draft,pending,confirmed,in_production,completed,cancelled',
            'status_notes' => 'nullable|string|max:500'
        ]);
        
        DB::beginTransaction();
        
        try {
            $oldStatus = $salesOrder->status;
            $newStatus = $request->status;
            
            $salesOrder->update([
                'status' => $newStatus
            ]);
            
            // Add status change note if provided
            if ($request->status_notes) {
                $salesOrder->notes()->create([
                    'content' => "Status changed from {$oldStatus} to {$newStatus}: " . $request->status_notes,
                    'type' => 'status_change',
                    'created_by' => Auth::id()
                ]);
            }
            
            // If status is 'in_production', create production tasks
            if ($newStatus === 'in_production') {
                $this->createProductionTasks($salesOrder);
            }
            
            // If status is 'completed', complete related tasks
            if ($newStatus === 'completed') {
                $this->completeSalesOrderTasks($salesOrder);
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($salesOrder)
                ->log("changed status from {$oldStatus} to {$newStatus}");
            
            DB::commit();
            
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('success', "Sales Order status updated to {$newStatus}.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
    
    public function createWorkOrder(Request $request, SalesOrder $salesOrder)
    {
        // Check if sales order can have work order created
        if (!in_array($salesOrder->status, ['confirmed', 'in_production'])) {
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'Work order can only be created for confirmed or in-production sales orders.');
        }
        
        // Check if work order already exists
        if ($salesOrder->workOrder()->exists()) {
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'Work order already exists for this sales order.');
        }
        
        DB::beginTransaction();
        
        try {
            // Create work order
            $workOrder = $salesOrder->workOrder()->create([
                'work_order_number' => $this->generateWorkOrderNumber(),
                'required_completion_date' => $salesOrder->delivery_date,
                'priority' => $this->calculatePriority($salesOrder),
                'status' => 'pending',
                'created_by' => Auth::id()
            ]);
            
            // Create production tasks from BOMs
            $boms = $salesOrder->boms()->where('status', 'approved')->get();
            
            foreach ($boms as $bom) {
                $this->createTasksFromBom($workOrder, $bom);
            }
            
            // Update sales order status if needed
            if ($salesOrder->status === 'confirmed') {
                $salesOrder->update(['status' => 'in_production']);
            }
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($salesOrder)
                ->withProperties(['work_order_id' => $workOrder->id])
                ->log('created work order');
            
            DB::commit();
            
            return redirect()->route('work-orders.show', $workOrder)
                ->with('success', 'Work Order created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('error', 'Failed to create work order: ' . $e->getMessage());
        }
    }
    
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:draft,pending,confirmed,in_production,completed,cancelled',
            'valve_category_id' => 'nullable|exists:valve_categories,id'
        ]);
        
        $query = SalesOrder::with(['valveCategory', 'createdBy']);
        
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->valve_category_id) {
            $query->where('valve_category_id', $request->valve_category_id);
        }
        
        $salesOrders = $query->orderBy('created_at', 'desc')->get();
        
        if ($request->format === 'pdf') {
            $pdf = \PDF::loadView('sales-orders.export.pdf', compact('salesOrders'));
            return $pdf->download('sales-orders-' . date('Y-m-d') . '.pdf');
        }
        
        // For Excel/CSV export, we'll use Laravel Excel package
        return (new \App\Exports\SalesOrdersExport($salesOrders))
            ->download('sales-orders-' . date('Y-m-d') . '.' . $request->format);
    }
    
    public function stats(Request $request)
    {
        $request->validate([
            'period' => 'required|in:today,week,month,quarter,year,custom',
            'start_date' => 'nullable|required_if:period,custom|date',
            'end_date' => 'nullable|required_if:period,custom|date|after_or_equal:start_date'
        ]);
        
        // Determine date range
        $dateRange = $this->getDateRange($request);
        
        // Get statistics
        $stats = $this->getSalesOrderStats($dateRange['start'], $dateRange['end']);
        
        // Get valve type distribution
        $valveTypeStats = SalesOrder::select('valve_type', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('valve_type')
            ->groupBy('valve_type')
            ->orderBy('count', 'desc')
            ->get();
        
        // Get monthly trend
        $monthlyTrend = SalesOrder::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Get status distribution
        $statusStats = SalesOrder::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('status')
            ->get();
        
        return response()->json([
            'stats' => $stats,
            'valve_type_stats' => $valveTypeStats,
            'monthly_trend' => $monthlyTrend,
            'status_stats' => $statusStats,
            'date_range' => $dateRange
        ]);
    }
    
    private function generateOrderNumber()
    {
        $prefix = 'SO';
        $year = date('Y');
        $month = date('m');
        
        $lastOrder = SalesOrder::where('order_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('order_number', 'desc')
            ->first();
        
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }
    
    private function generateWorkOrderNumber()
    {
        $prefix = 'WO';
        $year = date('Y');
        $month = date('m');
        
        $lastWorkOrder = \App\Models\WorkOrder::where('work_order_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('work_order_number', 'desc')
            ->first();
        
        if ($lastWorkOrder) {
            $lastNumber = intval(substr($lastWorkOrder->work_order_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }
    
    private function createBomFromSalesOrder(SalesOrder $salesOrder)
    {
        $bomNumber = 'BOM-' . $salesOrder->order_number . '-001';
        
        return Bom::create([
            'bom_number' => $bomNumber,
            'name' => "BOM for {$salesOrder->order_number} - {$salesOrder->valve_type}",
            'description' => "Bill of Materials for Sales Order {$salesOrder->order_number}",
            'sales_order_id' => $salesOrder->id,
            'status' => 'draft',
            'version' => 1,
            'is_current' => true,
            'created_by' => Auth::id()
        ]);
    }
    
    private function addItemsToBom(Bom $bom, array $items)
    {
        foreach ($items as $itemData) {
            // Find or create item
            $item = Item::firstOrCreate(
                ['item_code' => $itemData['item_code']],
                [
                    'name' => $itemData['name'],
                    'type' => 'raw_material',
                    'unit_of_measure' => $itemData['unit_of_measure'],
                    'unit_cost' => $itemData['unit_price'],
                    'created_by' => Auth::id()
                ]
            );
            
            // Add to BOM items
            $bom->items()->attach($item->id, [
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                'notes' => $itemData['notes'] ?? null
            ]);
        }
    }
    
    private function canEditSalesOrder(SalesOrder $salesOrder)
    {
        // Sales orders can only be edited in draft or pending status
        return in_array($salesOrder->status, ['draft', 'pending']);
    }
    
    private function calculatePriority(SalesOrder $salesOrder)
    {
        $daysUntilDelivery = Carbon::parse($salesOrder->delivery_date)->diffInDays(now());
        
        if ($daysUntilDelivery <= 3) {
            return 'high';
        } elseif ($daysUntilDelivery <= 7) {
            return 'medium';
        } else {
            return 'low';
        }
    }
    
    private function createProductionTasks(SalesOrder $salesOrder)
    {
        // Create default production tasks based on valve type
        $defaultTasks = [
            'Material Procurement' => 2,
            'Machining' => 3,
            'Assembly' => 2,
            'Testing' => 1,
            'Packaging' => 1
        ];
        
        $startDate = now();
        
        foreach ($defaultTasks as $taskName => $duration) {
            \App\Models\Task::create([
                'title' => "{$taskName} for {$salesOrder->order_number}",
                'description' => "{$taskName} task for valve type: {$salesOrder->valve_type}",
                'type' => 'production',
                'priority' => $this->calculatePriority($salesOrder),
                'assigned_to' => null, // To be assigned by supervisor
                'due_date' => $startDate->addDays($duration),
                'related_type' => 'sales_order',
                'related_id' => $salesOrder->id,
                'created_by' => Auth::id(),
                'status' => 'pending'
            ]);
        }
    }
    
    private function completeSalesOrderTasks(SalesOrder $salesOrder)
    {
        \App\Models\Task::where('related_type', 'sales_order')
            ->where('related_id', $salesOrder->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
    }
    
    private function createTasksFromBom($workOrder, Bom $bom)
    {
        // Create tasks based on BOM components
        $bomItems = $bom->items()->withPivot('quantity')->get();
        
        foreach ($bomItems as $item) {
            \App\Models\Task::create([
                'title' => "Procure {$item->pivot->quantity} x {$item->name}",
                'description' => "Item Code: {$item->item_code}, BOM: {$bom->bom_number}",
                'type' => 'procurement',
                'priority' => 'medium',
                'assigned_to' => null,
                'due_date' => $workOrder->required_completion_date,
                'related_type' => 'work_order',
                'related_id' => $workOrder->id,
                'created_by' => Auth::id(),
                'status' => 'pending'
            ]);
        }
    }
    
    private function getDateRange($request)
    {
        $now = Carbon::now();
        
        switch ($request->period) {
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
            case 'custom':
                return [
                    'start' => Carbon::parse($request->start_date)->startOfDay(),
                    'end' => Carbon::parse($request->end_date)->endOfDay()
                ];
            default:
                return [
                    'start' => $now->startOfMonth(),
                    'end' => $now->endOfMonth()
                ];
        }
    }
    
    private function getSalesOrderStats($startDate, $endDate)
    {
        return [
            'total_orders' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'avg_order_value' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->avg('total_amount'),
            'orders_by_status' => [
                'draft' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->where('status', 'draft')->count(),
                'pending' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
                'confirmed' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->where('status', 'confirmed')->count(),
                'in_production' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->where('status', 'in_production')->count(),
                'completed' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
                'cancelled' => SalesOrder::whereBetween('created_at', [$startDate, $endDate])->where('status', 'cancelled')->count(),
            ]
        ];
    }
}