<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemVersion extends Model
{
    protected $fillable = [
        'item_id',
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
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
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
        
        // Format specifications for display
        if (isset($data['specifications']) && is_string($data['specifications'])) {
            $data['specifications'] = json_decode($data['specifications'], true);
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

    public function getCostChangeAttribute()
    {
        $previous = ItemVersion::where('item_id', $this->item_id)
            ->where('version', '<', $this->version)
            ->orderBy('version', 'desc')
            ->first();

        if (!$previous || !isset($this->data['unit_cost'])) {
            return null;
        }

        $previousCost = $previous->data['unit_cost'] ?? 0;
        $currentCost = $this->data['unit_cost'];
        
        if ($previousCost == 0) {
            return null;
        }

        $change = $currentCost - $previousCost;
        $percentage = ($change / $previousCost) * 100;

        return [
            'amount' => $change,
            'percentage' => $percentage,
            'direction' => $change >= 0 ? 'increase' : 'decrease'
        ];
    }

    // Methods
    public function compareWithPrevious()
    {
        $previous = ItemVersion::where('item_id', $this->item_id)
            ->where('version', '<', $this->version)
            ->orderBy('version', 'desc')
            ->first();

        if (!$previous) {
            return [];
        }

        $changes = [];
        $currentData = $this->data;
        $previousData = $previous->data;

        // Compare key fields
        $keyFields = ['unit_cost', 'weight', 'material_grade', 'unit_of_measure', 'type'];
        
        foreach ($keyFields as $field) {
            if (isset($currentData[$field]) && isset($previousData[$field])) {
                if ($currentData[$field] != $previousData[$field]) {
                    $changes[$field] = [
                        'from' => $previousData[$field],
                        'to' => $currentData[$field]
                    ];
                }
            } elseif (isset($currentData[$field]) && !isset($previousData[$field])) {
                $changes[$field] = [
                    'from' => null,
                    'to' => $currentData[$field],
                    'added' => true
                ];
            } elseif (!isset($currentData[$field]) && isset($previousData[$field])) {
                $changes[$field] = [
                    'from' => $previousData[$field],
                    'to' => null,
                    'removed' => true
                ];
            }
        }

        // Compare specifications
        if (isset($currentData['specifications']) || isset($previousData['specifications'])) {
            $currentSpecs = $currentData['specifications'] ?? [];
            $previousSpecs = $previousData['specifications'] ?? [];
            
            if (is_string($currentSpecs)) {
                $currentSpecs = json_decode($currentSpecs, true) ?: [];
            }
            if (is_string($previousSpecs)) {
                $previousSpecs = json_decode($previousSpecs, true) ?: [];
            }

            $specChanges = $this->compareArrays($currentSpecs, $previousSpecs, 'specifications');
            if (!empty($specChanges)) {
                $changes['specifications'] = $specChanges;
            }
        }

        return $changes;
    }

    private function compareArrays($current, $previous, $prefix = '')
    {
        $changes = [];
        
        // Check for added or modified keys
        foreach ($current as $key => $value) {
            if (!isset($previous[$key])) {
                $changes["{$prefix}.{$key}"] = [
                    'from' => null,
                    'to' => $value,
                    'added' => true
                ];
            } elseif ($previous[$key] != $value) {
                $changes["{$prefix}.{$key}"] = [
                    'from' => $previous[$key],
                    'to' => $value
                ];
            }
        }

        // Check for removed keys
        foreach ($previous as $key => $value) {
            if (!isset($current[$key])) {
                $changes["{$prefix}.{$key}"] = [
                    'from' => $value,
                    'to' => null,
                    'removed' => true
                ];
            }
        }

        return $changes;
    }

    public function getSpecificFieldValue($field)
    {
        return $this->data[$field] ?? null;
    }
}