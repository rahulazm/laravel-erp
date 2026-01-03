<?php

namespace App\Http\Controllers;

use App\Models\Assembly;
use App\Models\Bom;
use App\Models\Item;
use App\Http\Requests\StoreAssemblyRequest;
use App\Http\Requests\UpdateAssemblyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssemblyController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create,assembly')->only(['create', 'store']);
        $this->middleware('can:update,assembly')->only(['edit', 'update']);
        $this->middleware('can:delete,assembly')->only(['destroy']);
    }
    
    public function index(Request $request)
    {
        $query = Assembly::with(['parent', 'createdBy'])
            ->withCount(['subAssemblies', 'boms']);
        
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $assemblies = $query->orderBy('code')->paginate(20);
        
        return view('assemblies.index', compact('assemblies'));
    }
    
    public function create(Request $request)
    {
        $parentId = $request->get('parent_id');
        $parent = $parentId ? Assembly::find($parentId) : null;
        
        // Get next assembly code
        $nextCode = $this->generateAssemblyCode();
        
        // Get available items for assembly components
        $availableItems = Item::where('type', '!=', 'finished_good')
            ->where('is_obsolete', false)
            ->orderBy('item_code')
            ->get();
        
        // Get sub-assembly options
        $subAssemblyOptions = Assembly::whereNull('parent_id')
            ->orWhere('id', '!=', $parentId)
            ->orderBy('name')
            ->get();
        
        return view('assemblies.create', compact(
            'parent',
            'nextCode',
            'availableItems',
            'subAssemblyOptions'
        ));
    }
    
    public function store(StoreAssemblyRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        
        // Create assembly
        $assembly = Assembly::create($data);
        
        // Attach components if provided
        if ($request->has('components')) {
            foreach ($request->components as $component) {
                if ($component['type'] === 'item') {
                    $assembly->items()->attach($component['id'], [
                        'quantity' => $component['quantity'],
                        'unit_of_measure' => $component['uom'] ?? 'each',
                        'notes' => $component['notes'] ?? null
                    ]);
                } elseif ($component['type'] === 'assembly') {
                    $assembly->subAssemblies()->attach($component['id'], [
                        'quantity' => $component['quantity'],
                        'notes' => $component['notes'] ?? null
                    ]);
                }
            }
        }
        
        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($assembly)
            ->log('created assembly');
        
        return redirect()->route('assemblies.show', $assembly)
            ->with('success', 'Assembly created successfully.');
    }
    
    public function show(Assembly $assembly)
    {
        $assembly->load([
            'parent',
            'createdBy',
            'items' => function($query) {
                $query->withPivot('quantity', 'unit_of_measure', 'notes');
            },
            'subAssemblies' => function($query) {
                $query->withPivot('quantity', 'notes');
            },
            'boms' => function($query) {
                $query->where('is_current', true)
                      ->with(['salesOrder', 'approvedBy']);
            }
        ]);
        
        // Calculate total cost
        $totalCost = $this->calculateAssemblyCost($assembly);
        
        // Get BOM statistics
        $bomStats = [
            'total' => $assembly->boms()->count(),
            'draft' => $assembly->boms()->where('status', 'draft')->count(),
            'pending' => $assembly->boms()->where('status', 'pending_approval')->count(),
            'approved' => $assembly->boms()->where('status', 'approved')->count(),
        ];
        
        // Get where this assembly is used as sub-assembly
        $usedIn = $assembly->usedInAssemblies()->with('parent')->get();
        
        return view('assemblies.show', compact(
            'assembly',
            'totalCost',
            'bomStats',
            'usedIn'
        ));
    }
    
    public function edit(Assembly $assembly)
    {
        // Get available items for assembly components
        $availableItems = Item::where('type', '!=', 'finished_good')
            ->where('is_obsolete', false)
            ->orderBy('item_code')
            ->get();
        
        // Get sub-assembly options (excluding current and its children)
        $subAssemblyOptions = Assembly::where('id', '!=', $assembly->id)
            ->where(function($query) use ($assembly) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', '!=', $assembly->id);
            })
            ->orderBy('name')
            ->get();
        
        // Load current components
        $assembly->load(['items', 'subAssemblies']);
        
        return view('assemblies.edit', compact(
            'assembly',
            'availableItems',
            'subAssemblyOptions'
        ));
    }
    
    public function update(UpdateAssemblyRequest $request, Assembly $assembly)
    {
        $data = $request->validated();
        
        // Update assembly
        $assembly->update($data);
        
        // Sync components if provided
        if ($request->has('components')) {
            $itemAttachments = [];
            $assemblyAttachments = [];
            
            foreach ($request->components as $component) {
                if ($component['type'] === 'item') {
                    $itemAttachments[$component['id']] = [
                        'quantity' => $component['quantity'],
                        'unit_of_measure' => $component['uom'] ?? 'each',
                        'notes' => $component['notes'] ?? null
                    ];
                } elseif ($component['type'] === 'assembly') {
                    $assemblyAttachments[$component['id']] = [
                        'quantity' => $component['quantity'],
                        'notes' => $component['notes'] ?? null
                    ];
                }
            }
            
            // Sync items and sub-assemblies
            $assembly->items()->sync($itemAttachments);
            $assembly->subAssemblies()->sync($assemblyAttachments);
        }
        
        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($assembly)
            ->log('updated assembly');
        
        return redirect()->route('assemblies.show', $assembly)
            ->with('success', 'Assembly updated successfully.');
    }
    
    public function destroy(Assembly $assembly)
    {
        // Check if assembly is used in BOMs or other assemblies
        if ($assembly->boms()->exists() || $assembly->usedInAssemblies()->exists()) {
            return redirect()->route('assemblies.show', $assembly)
                ->with('error', 'Cannot delete assembly that is used in BOMs or other assemblies.');
        }
        
        // Detach all components first
        $assembly->items()->detach();
        $assembly->subAssemblies()->detach();
        
        // Log activity before deletion
        activity()
            ->causedBy(Auth::user())
            ->performedOn($assembly)
            ->log('deleted assembly');
        
        $assembly->delete();
        
        return redirect()->route('assemblies.index')
            ->with('success', 'Assembly deleted successfully.');
    }
    
    public function exportBom(Assembly $assembly)
    {
        $assembly->load([
            'items' => function($query) {
                $query->withPivot('quantity', 'unit_of_measure', 'notes');
            },
            'subAssemblies' => function($query) {
                $query->withPivot('quantity', 'notes')
                      ->with(['items' => function($q) {
                          $q->withPivot('quantity', 'unit_of_measure');
                      }]);
            }
        ]);
        
        // Flatten BOM structure
        $flattenedBom = $this->flattenAssemblyBom($assembly);
        
        $pdf = \PDF::loadView('assemblies.export.bom-pdf', compact('assembly', 'flattenedBom'));
        return $pdf->download("bom-{$assembly->code}-" . date('Y-m-d') . '.pdf');
    }
    
    public function treeView(Assembly $assembly)
    {
        $assembly->load([
            'items' => function($query) {
                $query->withPivot('quantity', 'unit_of_measure');
            },
            'subAssemblies' => function($query) {
                $query->withPivot('quantity')
                      ->with(['items' => function($q) {
                          $q->withPivot('quantity', 'unit_of_measure');
                      }]);
            }
        ]);
        
        return view('assemblies.tree', compact('assembly'));
    }
    
    private function generateAssemblyCode()
    {
        $prefix = 'ASSY';
        $year = date('Y');
        
        $lastAssembly = Assembly::where('code', 'like', "{$prefix}-{$year}-%")
            ->orderBy('code', 'desc')
            ->first();
        
        if ($lastAssembly) {
            $lastNumber = intval(substr($lastAssembly->code, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$year}-{$newNumber}";
    }
    
    private function calculateAssemblyCost(Assembly $assembly)
    {
        $totalCost = 0;
        
        // Add cost of direct items
        foreach ($assembly->items as $item) {
            $totalCost += $item->pivot->quantity * $item->unit_cost;
        }
        
        // Add cost of sub-assemblies (recursive)
        foreach ($assembly->subAssemblies as $subAssembly) {
            $subAssemblyCost = $this->calculateAssemblyCost($subAssembly);
            $totalCost += $subAssembly->pivot->quantity * $subAssemblyCost;
        }
        
        return $totalCost;
    }
    
    private function flattenAssemblyBom(Assembly $assembly, $quantity = 1, $level = 0, $path = '')
    {
        $items = [];
        
        // Add direct items
        foreach ($assembly->items as $item) {
            $items[] = [
                'level' => $level,
                'type' => 'item',
                'code' => $item->item_code,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => $item->pivot->quantity * $quantity,
                'unit_of_measure' => $item->pivot->unit_of_measure,
                'unit_cost' => $item->unit_cost,
                'total_cost' => $item->pivot->quantity * $quantity * $item->unit_cost,
                'path' => $path . ' → ' . $assembly->name
            ];
        }
        
        // Add sub-assemblies (recursive)
        foreach ($assembly->subAssemblies as $subAssembly) {
            // Add sub-assembly header
            $items[] = [
                'level' => $level,
                'type' => 'assembly',
                'code' => $subAssembly->code,
                'name' => $subAssembly->name,
                'description' => $subAssembly->description,
                'quantity' => $subAssembly->pivot->quantity * $quantity,
                'unit_of_measure' => 'each',
                'unit_cost' => $this->calculateAssemblyCost($subAssembly),
                'total_cost' => $subAssembly->pivot->quantity * $quantity * $this->calculateAssemblyCost($subAssembly),
                'path' => $path . ' → ' . $assembly->name
            ];
            
            // Add sub-assembly components
            $subItems = $this->flattenAssemblyBom(
                $subAssembly,
                $subAssembly->pivot->quantity * $quantity,
                $level + 1,
                $path . ' → ' . $assembly->name
            );
            
            $items = array_merge($items, $subItems);
        }
        
        return $items;
    }
}