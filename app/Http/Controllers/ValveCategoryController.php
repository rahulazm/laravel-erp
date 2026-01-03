<?php

namespace App\Http\Controllers;

use App\Models\ValveCategory;
use App\Http\Requests\StoreValveCategoryRequest;
use App\Http\Requests\UpdateValveCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValveCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create,valve_category')->only(['create', 'store']);
        $this->middleware('can:update,valve_category')->only(['edit', 'update']);
        $this->middleware('can:delete,valve_category')->only(['destroy']);
    }
    
    public function index(Request $request)
    {
        $query = ValveCategory::withCount('salesOrders');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $valveCategories = $query->orderBy('name')->paginate(20);
        
        return view('valve-categories.index', compact('valveCategories'));
    }
    
    public function create()
    {
        return view('valve-categories.create');
    }
    
    public function store(StoreValveCategoryRequest $request)
    {
        $data = $request->validated();
        
        $valveCategory = ValveCategory::create($data);
        
        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($valveCategory)
            ->log('created valve category');
        
        return redirect()->route('valve-categories.show', $valveCategory)
            ->with('success', 'Valve category created successfully.');
    }
    
    public function show(ValveCategory $valveCategory)
    {
        $valveCategory->loadCount('salesOrders');
        
        // Get recent sales orders for this category
        $recentSalesOrders = $valveCategory->salesOrders()
            ->with(['createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get statistics
        $stats = [
            'total_orders' => $valveCategory->salesOrders()->count(),
            'pending_orders' => $valveCategory->salesOrders()->whereIn('status', ['draft', 'pending', 'confirmed'])->count(),
            'completed_orders' => $valveCategory->salesOrders()->where('status', 'completed')->count(),
            'avg_order_value' => $valveCategory->salesOrders()->avg('total_amount') ?? 0,
        ];
        
        return view('valve-categories.show', compact(
            'valveCategory',
            'recentSalesOrders',
            'stats'
        ));
    }
    
    public function edit(ValveCategory $valveCategory)
    {
        return view('valve-categories.edit', compact('valveCategory'));
    }
    
    public function update(UpdateValveCategoryRequest $request, ValveCategory $valveCategory)
    {
        $valveCategory->update($request->validated());
        
        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($valveCategory)
            ->log('updated valve category');
        
        return redirect()->route('valve-categories.show', $valveCategory)
            ->with('success', 'Valve category updated successfully.');
    }
    
    public function destroy(ValveCategory $valveCategory)
    {
        // Check if category is used in sales orders
        if ($valveCategory->salesOrders()->exists()) {
            return redirect()->route('valve-categories.show', $valveCategory)
                ->with('error', 'Cannot delete valve category that is used in sales orders.');
        }
        
        // Log activity before deletion
        activity()
            ->causedBy(Auth::user())
            ->performedOn($valveCategory)
            ->log('deleted valve category');
        
        $valveCategory->delete();
        
        return redirect()->route('valve-categories.index')
            ->with('success', 'Valve category deleted successfully.');
    }
    
    public function export(Request $request)
    {
        $valveCategories = ValveCategory::withCount('salesOrders')
            ->orderBy('name')
            ->get();
        
        if ($request->format === 'pdf') {
            $pdf = \PDF::loadView('valve-categories.export.pdf', compact('valveCategories'));
            return $pdf->download('valve-categories-' . date('Y-m-d') . '.pdf');
        }
        
        return (new \App\Exports\ValveCategoriesExport($valveCategories))
            ->download('valve-categories-' . date('Y-m-d') . '.' . $request->format);
    }
}