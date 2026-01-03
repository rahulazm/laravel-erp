<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assembly extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'parent_id',
        'drawing_number',
        'revision',
        'weight',
        'dimensions',
        'material',
        'surface_finish',
        'image_path',
        'document_path',
        'created_by',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weight' => 'decimal:2',
        'dimensions' => 'array'
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Assembly::class, 'parent_id');
    }

    public function subAssemblies(): HasMany
    {
        return $this->hasMany(Assembly::class, 'parent_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'assembly_items')
                    ->withPivot('quantity', 'unit_of_measure', 'notes', 'position')
                    ->withTimestamps();
    }

    public function usedInAssemblies(): BelongsToMany
    {
        return $this->belongsToMany(Assembly::class, 'assembly_sub_assemblies', 'sub_assembly_id', 'assembly_id')
                    ->withPivot('quantity', 'notes', 'position')
                    ->withTimestamps();
    }

    public function boms(): HasMany
    {
        return $this->hasMany(Bom::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }

    public function getLevelAttribute()
    {
        $level = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        
        return $level;
    }

    public function getBreadcrumbsAttribute()
    {
        $breadcrumbs = [];
        $current = $this;
        
        while ($current) {
            $breadcrumbs[] = [
                'id' => $current->id,
                'code' => $current->code,
                'name' => $current->name
            ];
            $current = $current->parent;
        }
        
        return array_reverse($breadcrumbs);
    }

    public function getTotalCostAttribute()
    {
        $totalCost = 0;
        
        // Add cost of direct items
        foreach ($this->items as $item) {
            $totalCost += $item->pivot->quantity * $item->unit_cost;
        }
        
        // Add cost of sub-assemblies (recursive)
        foreach ($this->subAssemblies as $subAssembly) {
            $totalCost += $subAssembly->pivot->quantity * $subAssembly->total_cost;
        }
        
        return $totalCost;
    }

    public function getComponentCountAttribute()
    {
        $count = $this->items()->count();
        
        foreach ($this->subAssemblies as $subAssembly) {
            $count += $subAssembly->component_count;
        }
        
        return $count;
    }

    // Scopes
    public function scopeMainAssemblies($query)
    {
        return $query->where('type', 'main')->whereNull('parent_id');
    }

    public function scopeSubAssemblies($query)
    {
        return $query->where('type', 'sub');
    }

    public function scopeComponents($query)
    {
        return $query->where('type', 'component');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('drawing_number', 'like', "%{$search}%");
        });
    }

    public function scopeWithComponents($query)
    {
        return $query->with(['items', 'subAssemblies']);
    }

    // Methods
    public function addItem($itemId, $quantity = 1, $unitOfMeasure = 'each', $notes = null, $position = null)
    {
        $this->items()->attach($itemId, [
            'quantity' => $quantity,
            'unit_of_measure' => $unitOfMeasure,
            'notes' => $notes,
            'position' => $position ?? $this->items()->count() + 1
        ]);
        
        return $this;
    }

    public function addSubAssembly($assemblyId, $quantity = 1, $notes = null, $position = null)
    {
        // Prevent circular reference
        if ($this->isDescendantOf($assemblyId)) {
            throw new \Exception('Circular reference detected');
        }
        
        $this->subAssemblies()->attach($assemblyId, [
            'quantity' => $quantity,
            'notes' => $notes,
            'position' => $position ?? $this->subAssemblies()->count() + 1
        ]);
        
        return $this;
    }

    public function isDescendantOf($assemblyId)
    {
        $current = $this->parent;
        
        while ($current) {
            if ($current->id == $assemblyId) {
                return true;
            }
            $current = $current->parent;
        }
        
        return false;
    }

    public function getFlattenedBom($quantity = 1, $level = 0)
    {
        $components = [];
        
        // Add direct items
        foreach ($this->items as $item) {
            $components[] = [
                'level' => $level,
                'type' => 'item',
                'id' => $item->id,
                'code' => $item->item_code,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => $item->pivot->quantity * $quantity,
                'unit_of_measure' => $item->pivot->unit_of_measure,
                'unit_cost' => $item->unit_cost,
                'total_cost' => $item->pivot->quantity * $quantity * $item->unit_cost,
                'notes' => $item->pivot->notes
            ];
        }
        
        // Add sub-assemblies recursively
        foreach ($this->subAssemblies as $subAssembly) {
            $components[] = [
                'level' => $level,
                'type' => 'assembly',
                'id' => $subAssembly->id,
                'code' => $subAssembly->code,
                'name' => $subAssembly->name,
                'description' => $subAssembly->description,
                'quantity' => $subAssembly->pivot->quantity * $quantity,
                'unit_of_measure' => 'each',
                'unit_cost' => $subAssembly->total_cost,
                'total_cost' => $subAssembly->pivot->quantity * $quantity * $subAssembly->total_cost,
                'notes' => $subAssembly->pivot->notes
            ];
            
            // Recursively get sub-assembly components
            $subComponents = $subAssembly->getFlattenedBom(
                $subAssembly->pivot->quantity * $quantity,
                $level + 1
            );
            
            $components = array_merge($components, $subComponents);
        }
        
        return $components;
    }

    public function getTreeStructure($maxDepth = 5, $currentDepth = 0)
    {
        if ($currentDepth >= $maxDepth) {
            return null;
        }
        
        $structure = [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'level' => $currentDepth,
            'items' => [],
            'subAssemblies' => []
        ];
        
        // Add items
        foreach ($this->items as $item) {
            $structure['items'][] = [
                'id' => $item->id,
                'code' => $item->item_code,
                'name' => $item->name,
                'quantity' => $item->pivot->quantity,
                'unit_of_measure' => $item->pivot->unit_of_measure,
                'unit_cost' => $item->unit_cost
            ];
        }
        
        // Add sub-assemblies recursively
        foreach ($this->subAssemblies as $subAssembly) {
            $subStructure = $subAssembly->getTreeStructure($maxDepth, $currentDepth + 1);
            if ($subStructure) {
                $subStructure['quantity'] = $subAssembly->pivot->quantity;
                $structure['subAssemblies'][] = $subStructure;
            }
        }
        
        return $structure;
    }

    public function updateRevision($newRevision, $changeNotes = null)
    {
        $this->update(['revision' => $newRevision]);
        
        if ($changeNotes) {
            // Log revision change
            activity()
                ->causedBy(auth()->user())
                ->performedOn($this)
                ->withProperties(['revision' => $newRevision, 'notes' => $changeNotes])
                ->log('updated revision');
        }
        
        return $this;
    }
}