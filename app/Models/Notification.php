<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'read_at',
        'action_url',
        'action_text',
        'related_type',
        'related_id',
        'priority',
        'expires_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    // Accessors
    public function getIsReadAttribute()
    {
        return $this->read_at !== null;
    }

    public function getIsUnreadAttribute()
    {
        return $this->read_at === null;
    }

    public function getPriorityClassAttribute()
    {
        $classes = [
            'critical' => 'danger',
            'high' => 'warning',
            'normal' => 'info',
            'low' => 'success'
        ];
        
        return $classes[$this->priority] ?? 'secondary';
    }

    public function getTypeIconAttribute()
    {
        $icons = [
            'approval' => 'fa-check-circle',
            'task_assignment' => 'fa-tasks',
            'task_completion' => 'fa-check',
            'task_overdue' => 'fa-exclamation-circle',
            'bom_created' => 'fa-list-alt',
            'bom_approved' => 'fa-thumbs-up',
            'bom_rejected' => 'fa-thumbs-down',
            'item_updated' => 'fa-edit',
            'item_obsolete' => 'fa-trash',
            'sales_order_created' => 'fa-shopping-cart',
            'sales_order_updated' => 'fa-edit',
            'checkout' => 'fa-sign-out-alt',
            'checkin' => 'fa-sign-in-alt',
            'system' => 'fa-cog',
            'alert' => 'fa-exclamation-triangle',
            'info' => 'fa-info-circle',
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-triangle',
            'error' => 'fa-times-circle'
        ];
        
        return $icons[$this->type] ?? 'fa-bell';
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getExpiredAttribute()
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return now()->greaterThan($this->expires_at);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')->where('expires_at', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActionable($query)
    {
        return $query->whereNotNull('action_url')
                     ->whereNotNull('action_text')
                     ->whereNull('read_at');
    }

    // Methods
    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
        
        return $this;
    }

    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
        return $this;
    }

    public static function createForUser($userId, $title, $message, $type = 'info', $data = null, $relatedType = null, $relatedId = null)
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'priority' => self::getDefaultPriority($type)
        ]);
    }

    public static function createForMultipleUsers($userIds, $title, $message, $type = 'info', $data = null)
    {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'priority' => self::getDefaultPriority($type),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        return self::insert($notifications);
    }

    public static function notifyAdmins($title, $message, $type = 'info', $data = null)
    {
        $adminIds = User::where('role', 'company_admin')->pluck('id');
        return self::createForMultipleUsers($adminIds, $title, $message, $type, $data);
    }

    public static function notifyManagers($title, $message, $type = 'info', $data = null)
    {
        $managerIds = User::whereIn('role', ['engineering_manager', 'production_manager', 'quality_manager'])
                         ->pluck('id');
        return self::createForMultipleUsers($managerIds, $title, $message, $type, $data);
    }

    public static function notifyDepartment($department, $title, $message, $type = 'info', $data = null)
    {
        $userIds = User::where('department', $department)->pluck('id');
        return self::createForMultipleUsers($userIds, $title, $message, $type, $data);
    }

    private static function getDefaultPriority($type)
    {
        $priorities = [
            'critical' => 'critical',
            'alert' => 'high',
            'warning' => 'high',
            'error' => 'high',
            'approval' => 'normal',
            'task_overdue' => 'high',
            'task_assignment' => 'normal',
            'bom_rejected' => 'normal',
            'system' => 'low',
            'info' => 'low',
            'success' => 'low'
        ];
        
        return $priorities[$type] ?? 'normal';
    }

    public function setAction($url, $text)
    {
        $this->update([
            'action_url' => $url,
            'action_text' => $text
        ]);
        
        return $this;
    }

    public function setExpiry($days)
    {
        $this->update(['expires_at' => now()->addDays($days)]);
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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'priority' => $this->priority,
            'is_read' => $this->is_read,
            'time_ago' => $this->time_ago,
            'action_url' => $this->action_url,
            'action_text' => $this->action_text,
            'created_at' => $this->created_at->toDateTimeString(),
            'icon' => $this->type_icon,
            'priority_class' => $this->priority_class
        ];
    }
}