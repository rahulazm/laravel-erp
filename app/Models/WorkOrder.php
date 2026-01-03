<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'work_order_number',
        'sales_order_id',
        'required_completion_date',
        'priority',
        'status',
        'assigned_to',
        'started_at',
        'completed_at',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'required_completion_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'related_id')
                    ->where('related_type', 'work_order');
    }
}