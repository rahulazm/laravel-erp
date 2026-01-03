<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bom extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bom_number',
        'name',
        'description',
        'assembly_id',
        'sales_order_id',
        'status',
        'version',
        'is_current',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_current' => 'boolean'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function assembly(): BelongsTo
    {
        return $this->belongsTo(Assembly::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(BomVersion::class);
    }

    public function checkouts(): HasMany
    {
        return $this->morphMany(Checkout::class, 'checkoutable');
    }

    public function getCurrentCheckoutAttribute()
    {
        return $this->checkouts()->whereNull('checked_in_at')->first();
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}