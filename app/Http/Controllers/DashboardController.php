<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Bom;
use App\Models\Item;
use App\Models\Checkout;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Statistics for charts
        $salesOrdersCount = SalesOrder::count();
        $bomsCount = Bom::count();
        $itemsCount = Item::count();
        $obsoleteItemsCount = Item::obsolete()->count();
        
        // Monthly data for charts
        $monthlySalesOrders = SalesOrder::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('count', 'month');
        
        $monthlyBoms = Bom::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('count', 'month');
        
        $monthlyItems = Item::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('count', 'month');
        
        // Checked out items by current user
        $checkedOutItems = Checkout::with('checkoutable')
            ->byUser($user->id)
            ->active()
            ->get();
        
        // Pending tasks
        $pendingTasks = Task::where('assigned_to', $user->id)
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->limit(5)
            ->get();
        
        // Notifications
        $notifications = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Recent activities
        $recentSalesOrders = SalesOrder::with('createdBy')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentBoms = Bom::with('createdBy')
            ->where('is_current', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Average approval time for BOMs
        $averageApprovalTime = Bom::approved()
            ->whereNotNull('approved_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours'))
            ->first()
            ->avg_hours ?? 0;
        
        return view('dashboard.index', compact(
            'salesOrdersCount',
            'bomsCount',
            'itemsCount',
            'obsoleteItemsCount',
            'monthlySalesOrders',
            'monthlyBoms',
            'monthlyItems',
            'checkedOutItems',
            'pendingTasks',
            'notifications',
            'recentSalesOrders',
            'recentBoms',
            'averageApprovalTime'
        ));
    }
    
    public function search(Request $request)
    {
        $query = $request->get('query');
        $type = $request->get('type', 'all');
        
        $results = collect();
        
        if ($type === 'all' || $type === 'sales_order') {
            $salesOrders = SalesOrder::where('order_number', 'like', "%{$query}%")
                ->orWhere('customer_name', 'like', "%{$query}%")
                ->orWhere('valve_type', 'like', "%{$query}%")
                ->limit(10)
                ->get();
            $results = $results->merge($salesOrders->map(function($item) {
                $item->type = 'Sales Order';
                $item->route = route('sales-orders.show', $item);
                return $item;
            }));
        }
        
        if ($type === 'all' || $type === 'bom') {
            $boms = Bom::where('bom_number', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%")
                ->where('is_current', true)
                ->limit(10)
                ->get();
            $results = $results->merge($boms->map(function($item) {
                $item->type = 'BOM';
                $item->route = route('boms.show', $item);
                return $item;
            }));
        }
        
        if ($type === 'all' || $type === 'item') {
            $items = Item::where('item_code', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%")
                ->where('is_current', true)
                ->limit(10)
                ->get();
            $results = $results->merge($items->map(function($item) {
                $item->type = 'Item';
                $item->route = route('items.show', $item);
                return $item;
            }));
        }
        
        if ($type === 'all' || $type === 'valve_category') {
            $valveCategories = ValveCategory::where('name', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%")
                ->limit(10)
                ->get();
            $results = $results->merge($valveCategories->map(function($item) {
                $item->type = 'Valve Category';
                $item->route = route('valve-categories.show', $item);
                return $item;
            }));
        }
        
        return response()->json($results);
    }
}