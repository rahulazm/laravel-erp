<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ValveCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'pressure_rating',
        'temperature_rating',
        'material',
        'end_connection',
        'standard',
        'size_range',
        'image_path',
        'created_by',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'size_range' => 'array'
    ];

    // Relationships
    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }

    public function getSpecificationsAttribute()
    {
        $specs = [];
        
        if ($this->pressure_rating) {
            $specs[] = "Pressure: {$this->pressure_rating}";
        }
        
        if ($this->temperature_rating) {
            $specs[] = "Temperature: {$this->temperature_rating}";
        }
        
        if ($this->material) {
            $specs[] = "Material: {$this->material}";
        }
        
        if ($this->end_connection) {
            $specs[] = "Connection: {$this->end_connection}";
        }
        
        if ($this->standard) {
            $specs[] = "Standard: {$this->standard}";
        }
        
        return implode(' | ', $specs);
    }

    public function getStatsAttribute()
    {
        return [
            'total_orders' => $this->salesOrders()->count(),
            'active_orders' => $this->salesOrders()->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'total_revenue' => $this->salesOrders()->sum('total_amount'),
            'avg_order_value' => $this->salesOrders()->avg('total_amount') ?? 0
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('material', 'like', "%{$search}%")
              ->orWhere('standard', 'like', "%{$search}%");
        });
    }

    public function scopeWithOrderStats($query)
    {
        return $query->withCount(['salesOrders as total_orders'])
                     ->withCount(['salesOrders as active_orders' => function($q) {
                         $q->whereNotIn('status', ['completed', 'cancelled']);
                     }])
                     ->withCount(['salesOrders as completed_orders' => function($q) {
                         $q->where('status', 'completed');
                     }]);
    }

    // Methods
    public function hasActiveOrders()
    {
        return $this->salesOrders()->whereNotIn('status', ['completed', 'cancelled'])->exists();
    }

    public function canBeDeleted()
    {
        return !$this->salesOrders()->exists();
    }

    public function deactivate()
    {
        if ($this->hasActiveOrders()) {
            throw new \Exception('Cannot deactivate category with active orders');
        }
        
        $this->update(['is_active' => false]);
        return $this;
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
        return $this;
    }

    public function getRelatedBoms()
    {
        $salesOrderIds = $this->salesOrders()->pluck('id');
        return Bom::whereIn('sales_order_id', $salesOrderIds)->get();
    }

    public function getPopularValveTypes($limit = 5)
    {
        return $this->salesOrders()
            ->select('valve_type', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('valve_type')
            ->groupBy('valve_type')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function updateStats()
    {
        // This method can be used to update cached statistics
        $stats = $this->stats;
        // You could store these in a separate field if needed
        return $stats;
    }
}