<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemVersion;
use App\Models\Checkout;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\VersionService;

class ItemController extends Controller
{
    protected $versionService;
    
    public function __construct(VersionService $versionService)
    {
        $this->versionService = $versionService;
        $this->middleware('can:create,item')->only(['create', 'store']);
        $this->middleware('can:update,item')->only(['edit', 'update']);
        $this->middleware('can:delete,item')->only(['destroy']);
    }
    
    public function index(Request $request)
    {
        $query = Item::with(['createdBy'])
            ->where('is_current', true);
            
        // Apply filters
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_obsolete', false);
            } elseif ($request->status === 'obsolete') {
                $query->where('is_obsolete', true);
            }
        }
        
        if ($request->has('material_grade') && $request->material_grade) {
            $query->where('material_grade', $request->material_grade);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortField = $request->get('sort', 'item_code');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);
        
        $items = $query->paginate(25)->withQueryString();
        
        // Get filter options
        $itemTypes = Item::distinct()->pluck('type')->filter();
        $materialGrades = Item::distinct()->whereNotNull('material_grade')->pluck('material_grade')->filter();
        
        // Statistics
        $totalItems = Item::where('is_current', true)->count();
        $activeItems = Item::where('is_current', true)->where('is_obsolete', false)->count();
        $obsoleteItems = Item::where('is_current', true)->where('is_obsolete', true)->count();
        
        return view('items.index', compact(
            'items',
            'itemTypes',
            'materialGrades',
            'totalItems',
            'activeItems',
            'obsoleteItems'
        ));
    }
    
    public function create()
    {
        // Get next item code suggestion
        $nextItemCode = $this->generateItemCode();
        
        // Get existing material grades for suggestions
        $materialGrades = Item::distinct()
            ->whereNotNull('material_grade')
            ->pluck('material_grade');
            
        // Get existing specifications keys
        $specKeys = Item::whereNotNull('specifications')
            ->get()
            ->flatMap(function($item) {
                return array_keys(json_decode($item->specifications, true) ?: []);
            })
            ->unique()
            ->values();
        
        return view('items.create', compact(
            'nextItemCode',
            'materialGrades',
            'specKeys'
        ));
    }
    
    public function store(StoreItemRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $data['version'] = 1;
            $data['is_current'] = true;
            
            // Process specifications if provided
            if ($request->has('specifications')) {
                $specs = [];
                foreach ($request->specifications['key'] as $index => $key) {
                    if (!empty($key) && !empty($request->specifications['value'][$index])) {
                        $specs[$key] = $request->specifications['value'][$index];
                    }
                }
                $data['specifications'] = !empty($specs) ? json_encode($specs) : null;
            }
            
            // Create item
            $item = Item::create($data);
            
            // Create initial version
            $this->versionService->createVersion($item, $data);
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($item)
                ->log('created item');
            
            DB::commit();
            
            return redirect()->route('items.show', $item)
                ->with('success', 'Item created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create item: ' . $e->getMessage());
        }
    }
    
    public function show(Item $item)
    {
        $item->load(['createdBy', 'versions']);
        
        // Get checkout status
        $checkout = $item->currentCheckout;
        
        // Get usage statistics (where this item is used)
        $bomUsage = DB::table('bom_item')
            ->join('boms', 'bom_item.bom_id', '=', 'boms.id')
            ->where('bom_item.item_id', $item->id)
            ->where('boms.is_current', true)
            ->select('boms.*', 'bom_item.quantity')
            ->get();
        
        // Get recent changes
        $recentVersions = $item->versions()
            ->orderBy('version', 'desc')
            ->limit(5)
            ->get();
        
        return view('items.show', compact(
            'item',
            'checkout',
            'bomUsage',
            'recentVersions'
        ));
    }
    
    public function edit(Item $item)
    {
        // Check if item is checked out by current user
        $checkout = $item->currentCheckout;
        if ($checkout && $checkout->user_id !== Auth::id()) {
            return redirect()->route('items.show', $item)
                ->with('error', 'This item is currently checked out by another user.');
        }
        
        // Get existing material grades for suggestions
        $materialGrades = Item::distinct()
            ->whereNotNull('material_grade')
            ->where('id', '!=', $item->id)
            ->pluck('material_grade');
            
        // Get existing specifications keys
        $specKeys = Item::whereNotNull('specifications')
            ->where('id', '!=', $item->id)
            ->get()
            ->flatMap(function($it) {
                return array_keys(json_decode($it->specifications, true) ?: []);
            })
            ->unique()
            ->values();
        
        return view('items.edit', compact(
            'item',
            'materialGrades',
            'specKeys'
        ));
    }
    
    public function update(UpdateItemRequest $request, Item $item)
    {
        // Check if item is checked out by current user
        $checkout = $item->currentCheckout;
        if (!$checkout || $checkout->user_id !== Auth::id()) {
            return redirect()->route('items.show', $item)
                ->with('error', 'You must check out the item before editing.');
        }
        
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Process specifications if provided
            if ($request->has('specifications')) {
                $specs = [];
                foreach ($request->specifications['key'] as $index => $key) {
                    if (!empty($key) && !empty($request->specifications['value'][$index])) {
                        $specs[$key] = $request->specifications['value'][$index];
                    }
                }
                $data['specifications'] = !empty($specs) ? json_encode($specs) : null;
            }
            
            // Create new version
            $newItem = $this->versionService->createNewVersion(
                $item, 
                $data, 
                $request->changes_description
            );
            
            // Check in the item
            $checkout->checkin();
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($newItem)
                ->withProperties(['old_version' => $item->version])
                ->log('updated item');
            
            DB::commit();
            
            return redirect()->route('items.show', $newItem)
                ->with('success', 'Item updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update item: ' . $e->getMessage());
        }
    }
    
    public function destroy(Item $item)
    {
        // Check if item is used in any BOMs
        $usageCount = DB::table('bom_item')->where('item_id', $item->id)->count();
        
        if ($usageCount > 0) {
            return redirect()->route('items.show', $item)
                ->with('error', 'Cannot delete item that is used in BOMs. Mark as obsolete instead.');
        }
        
        DB::beginTransaction();
        
        try {
            // Log activity before deletion
            activity()
                ->causedBy(Auth::user())
                ->performedOn($item)
                ->log('deleted item');
            
            $item->delete();
            
            DB::commit();
            
            return redirect()->route('items.index')
                ->with('success', 'Item deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('items.show', $item)
                ->with('error', 'Failed to delete item: ' . $e->getMessage());
        }
    }
    
    public function checkout(Request $request, Item $item)
    {
        // Check if already checked out
        if ($item->currentCheckout) {
            return redirect()->route('items.show', $item)
                ->with('error', 'Item is already checked out.');
        }
        
        // Create checkout record
        $checkout = $item->checkouts()->create([
            'user_id' => Auth::id(),
            'checked_out_at' => now(),
            'purpose' => $request->purpose
        ]);
        
        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($item)
            ->log('checked out item');
        
        return redirect()->route('items.edit', $item)
            ->with('success', 'Item checked out successfully.');
    }
    
    public function checkin(Request $request, Item $item)
    {
        $checkout = $item->currentCheckout;
        
        if (!$checkout || $checkout->user_id !== Auth::id()) {
            return redirect()->route('items.show', $item)
                ->with('error', 'You cannot check in this item.');
        }
        
        $checkout->checkin();
        
        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($item)
            ->log('checked in item');
        
        return redirect()->route('items.show', $item)
            ->with('success', 'Item checked in successfully.');
    }
    
    public function markAsObsolete(Request $request, Item $item)
    {
        $request->validate([
            'obsolete_reason' => 'required|string|max:500'
        ]);
        
        DB::beginTransaction();
        
        try {
            $item->markAsObsolete();
            
            // Add obsolete note
            $item->notes()->create([
                'content' => $request->obsolete_reason,
                'type' => 'obsolete',
                'created_by' => Auth::id()
            ]);
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($item)
                ->log('marked item as obsolete');
            
            DB::commit();
            
            return redirect()->route('items.show', $item)
                ->with('success', 'Item marked as obsolete.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('items.show', $item)
                ->with('error', 'Failed to mark item as obsolete: ' . $e->getMessage());
        }
    }
    
    public function restoreFromObsolete(Request $request, Item $item)
    {
        $request->validate([
            'restore_reason' => 'required|string|max:500'
        ]);
        
        DB::beginTransaction();
        
        try {
            $item->restoreFromObsolete();
            
            // Add restore note
            $item->notes()->create([
                'content' => $request->restore_reason,
                'type' => 'restore',
                'created_by' => Auth::id()
            ]);
            
            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->performedOn($item)
                ->log('restored item from obsolete');
            
            DB::commit();
            
            return redirect()->route('items.show', $item)
                ->with('success', 'Item restored from obsolete.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('items.show', $item)
                ->with('error', 'Failed to restore item: ' . $e->getMessage());
        }
    }
    
    public function obsolete(Request $request)
    {
        $query = Item::with(['createdBy'])
            ->where('is_current', true)
            ->where('is_obsolete', true);
            
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        $obsoleteItems = $query->orderBy('updated_at', 'desc')->paginate(25);
        
        return view('items.obsolete', compact('obsoleteItems'));
    }
    
    public function versions(Item $item)
    {
        $versions = $item->versions()->orderBy('version', 'desc')->get();
        
        return view('items.versions', compact('item', 'versions'));
    }
    
    public function restoreVersion(Item $item, $version)
    {
        $oldVersion = $item->versions()->where('version', $version)->firstOrFail();
        
        // Create new version from old version data
        $newItem = $this->versionService->createNewVersion(
            $item,
            $oldVersion->data,
            "Restored from version {$version}"
        );
        
        return redirect()->route('items.show', $newItem)
            ->with('success', "Item restored from version {$version} successfully.");
    }
    
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf',
            'status' => 'nullable|in:active,obsolete,all',
            'type' => 'nullable|string'
        ]);
        
        $query = Item::with(['createdBy'])->where('is_current', true);
        
        if ($request->status && $request->status !== 'all') {
            $query->where('is_obsolete', $request->status === 'obsolete');
        }
        
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        $items = $query->orderBy('item_code')->get();
        
        if ($request->format === 'pdf') {
            $pdf = \PDF::loadView('items.export.pdf', compact('items'));
            return $pdf->download('items-' . date('Y-m-d') . '.pdf');
        }
        
        return (new \App\Exports\ItemsExport($items))
            ->download('items-' . date('Y-m-d') . '.' . $request->format);
    }
    
    private function generateItemCode()
    {
        $prefix = 'ITEM';
        $year = date('Y');
        
        $lastItem = Item::where('item_code', 'like', "{$prefix}-{$year}-%")
            ->orderBy('item_code', 'desc')
            ->first();
        
        if ($lastItem) {
            $lastNumber = intval(substr($lastItem->item_code, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}-{$newNumber}";
    }
}