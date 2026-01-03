<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Checkout extends Model
{
    protected $fillable = [
        'checkoutable_type',
        'checkoutable_id',
        'user_id',
        'checked_out_at',
        'checked_in_at',
        'purpose'
    ];

    protected $casts = [
        'checked_out_at' => 'datetime',
        'checked_in_at' => 'datetime'
    ];

    public function checkoutable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkin()
    {
        $this->update(['checked_in_at' => now()]);
        return $this;
    }

    public function scopeActive($query)
    {
        return $query->whereNull('checked_in_at');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}