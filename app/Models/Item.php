<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_code',
        'name',
        'description',
        'type',
        'unit_of_measure',
        'unit_cost',
        'weight',
        'material_grade',
        'specifications',
        'version',
        'is_current',
        'is_obsolete',
        'created_by'
    ];

    protected $casts = [
        'specifications' => 'array',
        'unit_cost' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_current' => 'boolean',
        'is_obsolete' => 'boolean'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ItemVersion::class);
    }

    public function checkouts(): HasMany
    {
        return $this->morphMany(Checkout::class, 'checkoutable');
    }

    public function getCurrentCheckoutAttribute()
    {
        return $this->checkouts()->whereNull('checked_in_at')->first();
    }

    public function scopeObsolete($query)
    {
        return $query->where('is_obsolete', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_obsolete', false);
    }

    public function markAsObsolete()
    {
        $this->update(['is_obsolete' => true]);
        return $this;
    }

    public function restoreFromObsolete()
    {
        $this->update(['is_obsolete' => false]);
        return $this;
    }
}