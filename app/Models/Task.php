<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'priority',
        'status',
        'assigned_to',
        'assigned_by',
        'due_date',
        'completed_at',
        'completed_by',
        'completion_notes',
        'estimated_hours',
        'actual_hours',
        'related_type',
        'related_id',
        'created_by',
        'checklist',
        'attachments',
        'tags'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'checklist' => 'array',
        'attachments' => 'array',
        'tags' => 'array'
    ];

    // Relationships
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    // Accessors
    public function getPriorityClassAttribute()
    {
        $classes = [
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'success'
        ];
        
        return $classes[$this->priority] ?? 'secondary';
    }

    public function getStatusClassAttribute()
    {
        $classes = [
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            'on_hold' => 'secondary'
        ];
        
        return $classes[$this->status] ?? 'secondary';
    }

    public function getTypeIconAttribute()
    {
        $icons = [
            'approval' => 'fa-check-circle',
            'production' => 'fa-industry',
            'procurement' => 'fa-shopping-cart',
            'quality' => 'fa-clipboard-check',
            'design' => 'fa-drafting-compass',
            'documentation' => 'fa-file-alt',
            'maintenance' => 'fa-tools',
            'meeting' => 'fa-users',
            'training' => 'fa-graduation-cap',
            'other' => 'fa-tasks'
        ];
        
        return $icons[$this->type] ?? 'fa-tasks';
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->status === 'completed' || $this->status === 'cancelled') {
            return null;
        }
        
        if (!$this->due_date) {
            return null;
        }
        
        return now()->diffInDays($this->due_date, false);
    }

    public function getIsOverdueAttribute()
    {
        if ($this->status === 'completed' || $this->status === 'cancelled') {
            return false;
        }
        
        if (!$this->due_date) {
            return false;
        }
        
        return now()->greaterThan($this->due_date);
    }

    public function getProgressAttribute()
    {
        if ($this->checklist && is_array($this->checklist)) {
            $total = count($this->checklist);
            $completed = count(array_filter($this->checklist, function($item) {
                return $item['completed'] ?? false;
            }));
            
            return $total > 0 ? round(($completed / $total) * 100) : 0;
        }
        
        return $this->status === 'completed' ? 100 : 0;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
                     ->where('status', '!=', 'cancelled')
                     ->where('due_date', '<', now());
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeDueBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('due_date', [$startDate, $endDate]);
    }

    public function scopeRelatedTo($query, $type, $id)
    {
        return $query->where('related_type', $type)->where('related_id', $id);
    }

    // Methods
    public function assignTo($userId, $assignedById = null)
    {
        $this->update([
            'assigned_to' => $userId,
            'assigned_by' => $assignedById ?? auth()->id(),
            'status' => 'pending'
        ]);
        
        // Create notification for assigned user
        Notification::create([
            'user_id' => $userId,
            'title' => 'New Task Assigned',
            'message' => "You have been assigned a new task: {$this->title}",
            'type' => 'task_assignment',
            'related_type' => 'task',
            'related_id' => $this->id
        ]);
        
        return $this;
    }

    public function startProgress()
    {
        $this->update(['status' => 'in_progress']);
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($this)
            ->log('started task');
            
        return $this;
    }

    public function complete($notes = null, $completedById = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => $completedById ?? auth()->id(),
            'completion_notes' => $notes
        ]);
        
        // Mark all checklist items as completed
        if ($this->checklist) {
            $checklist = $this->checklist;
            foreach ($checklist as &$item) {
                $item['completed'] = true;
                $item['completed_at'] = now()->toDateTimeString();
            }
            $this->update(['checklist' => $checklist]);
        }
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($this)
            ->withProperties(['notes' => $notes])
            ->log('completed task');
            
        return $this;
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'completion_notes' => $reason
        ]);
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($this)
            ->withProperties(['reason' => $reason])
            ->log('cancelled task');
            
        return $this;
    }

    public function updateChecklistItem($index, $completed, $notes = null)
    {
        if (!$this->checklist || !isset($this->checklist[$index])) {
            return false;
        }
        
        $checklist = $this->checklist;
        $checklist[$index]['completed'] = $completed;
        $checklist[$index]['updated_at'] = now()->toDateTimeString();
        
        if ($notes) {
            $checklist[$index]['notes'] = $notes;
        }
        
        $this->update(['checklist' => $checklist]);
        
        // If all items are completed, auto-complete the task
        $allCompleted = true;
        foreach ($checklist as $item) {
            if (!($item['completed'] ?? false)) {
                $allCompleted = false;
                break;
            }
        }
        
        if ($allCompleted && $this->status !== 'completed') {
            $this->complete('Auto-completed: All checklist items completed');
        }
        
        return $this;
    }

    public function addAttachment($path, $name = null, $type = null)
    {
        $attachments = $this->attachments ?? [];
        
        $attachments[] = [
            'path' => $path,
            'name' => $name ?? basename($path),
            'type' => $type ?? pathinfo($path, PATHINFO_EXTENSION),
            'uploaded_at' => now()->toDateTimeString(),
            'uploaded_by' => auth()->id()
        ];
        
        $this->update(['attachments' => $attachments]);
        
        return $this;
    }

    public function logTime($hours, $date = null, $notes = null)
    {
        $this->actual_hours = ($this->actual_hours ?? 0) + $hours;
        $this->save();
        
        // Create time log entry
        TimeLog::create([
            'task_id' => $this->id,
            'user_id' => auth()->id(),
            'hours' => $hours,
            'date' => $date ?? now(),
            'notes' => $notes
        ]);
        
        return $this;
    }

    public function getRelatedEntity()
    {
        if (!$this->related_type || !$this->related_id) {
            return null;
        }
        
        $modelClass = 'App\\Models\\' . ucfirst($this->related_type);
        
        if (class_exists($modelClass)) {
            return $modelClass::find($this->related_id);
        }
        
        return null;
    }
}