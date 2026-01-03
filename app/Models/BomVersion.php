<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomVersion extends Model
{
    protected $fillable = [
        'bom_id',
        'version',
        'data',
        'changes_description',
        'created_by'
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getVersionLabelAttribute()
    {
        return "v{$this->version}";
    }

    public function getFormattedDataAttribute()
    {
        $data = $this->data;
        
        // Format the data for display
        if (isset($data['components'])) {
            $data['components'] = array_map(function($component) {
                return [
                    'item_code' => $component['item_code'] ?? 'N/A',
                    'name' => $component['name'] ?? 'N/A',
                    'quantity' => $component['quantity'] ?? 0,
                    'unit_of_measure' => $component['unit_of_measure'] ?? 'each',
                    'unit_cost' => $component['unit_cost'] ?? 0,
                    'total_cost' => ($component['quantity'] ?? 0) * ($component['unit_cost'] ?? 0)
                ];
            }, $data['components']);
        }

        return $data;
    }

    public function getChangesSummaryAttribute()
    {
        if (!$this->changes_description) {
            return 'No change description provided';
        }

        return $this->changes_description;
    }

    // Methods
    public function getFieldChange($field)
    {
        return $this->data[$field] ?? null;
    }

    public function compareWithPrevious()
    {
        $previous = BomVersion::where('bom_id', $this->bom_id)
            ->where('version', '<', $this->version)
            ->orderBy('version', 'desc')
            ->first();

        if (!$previous) {
            return [];
        }

        $changes = [];
        $currentData = $this->data;
        $previousData = $previous->data;

        foreach ($currentData as $key => $value) {
            if (!isset($previousData[$key]) || $previousData[$key] != $value) {
                $changes[$key] = [
                    'from' => $previousData[$key] ?? null,
                    'to' => $value
                ];
            }
        }

        // Check for removed fields
        foreach ($previousData as $key => $value) {
            if (!isset($currentData[$key])) {
                $changes[$key] = [
                    'from' => $value,
                    'to' => null,
                    'removed' => true
                ];
            }
        }

        return $changes;
    }

    public function restoreToThisVersion()
    {
        // This would create a new version from this version's data
        return $this->bom->createNewVersionFrom($this);
    }
}