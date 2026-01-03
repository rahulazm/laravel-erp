<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'valve_type',
        'valve_category_id',
        'total_amount',
        'status',
        'delivery_date',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function valveCategory(): BelongsTo
    {
        return $this->belongsTo(ValveCategory::class);
    }

    public function boms(): HasMany
    {
        return $this->hasMany(Bom::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'completed']);
    }

    // Add these relationships to your existing SalesOrder.php model

public function workOrder()
{
    return $this->hasOne(WorkOrder::class);
}

public function notes()
{
    return $this->morphMany(Note::class, 'noteable');
}

public function activities()
{
    return $this->morphMany(Activity::class, 'subject');
}
}