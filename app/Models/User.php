<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'department',
        'designation',
        'phone',
        'role',
        'notification_settings',
        'is_active',
        'last_login_at',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'notification_settings' => 'array'
    ];

    // Relationships
    public function createdSalesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'created_by');
    }

    public function createdBoms()
    {
        return $this->hasMany(Bom::class, 'created_by');
    }

    public function approvedBoms()
    {
        return $this->hasMany(Bom::class, 'approved_by');
    }

    public function createdItems()
    {
        return $this->hasMany(Item::class, 'created_by');
    }

    public function checkouts()
    {
        return $this->hasMany(Checkout::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeEngineers($query)
    {
        return $query->whereIn('department', ['engineering', 'production', 'quality']);
    }

    public function scopeApprovers($query)
    {
        return $query->whereIn('role', ['company_admin', 'engineering_manager', 'production_manager']);
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'company_admin';
    }

    public function isManager()
    {
        return in_array($this->role, ['engineering_manager', 'production_manager', 'quality_manager']);
    }

    public function canApproveBoms()
    {
        return $this->isAdmin() || $this->role === 'engineering_manager';
    }

    public function getActiveCheckouts()
    {
        return $this->checkouts()->active()->with('checkoutable')->get();
    }

    public function getPendingTasks()
    {
        return $this->tasks()->where('status', 'pending')->orderBy('due_date')->get();
    }

    public function getUnreadNotifications()
    {
        return $this->notifications()->whereNull('read_at')->orderBy('created_at', 'desc')->get();
    }

    public function getNotificationSetting($key, $default = null)
    {
        $settings = $this->notification_settings ?? [];
        return $settings[$key] ?? $default;
    }

    public function updateNotificationSetting($key, $value)
    {
        $settings = $this->notification_settings ?? [];
        $settings[$key] = $value;
        $this->update(['notification_settings' => $settings]);
        return $this;
    }

    public function recordLogin()
    {
        $this->update(['last_login_at' => now()]);
        return $this;
    }

    public function getRoleBadgeClass()
    {
        $classes = [
            'company_admin' => 'bg-danger',
            'engineering_manager' => 'bg-primary',
            'production_manager' => 'bg-success',
            'quality_manager' => 'bg-warning',
            'engineer' => 'bg-info',
            'operator' => 'bg-secondary',
            'viewer' => 'bg-light text-dark'
        ];

        return $classes[$this->role] ?? 'bg-secondary';
    }

    public function getDepartmentBadgeClass()
    {
        $classes = [
            'engineering' => 'badge bg-primary',
            'production' => 'badge bg-success',
            'quality' => 'badge bg-warning',
            'purchase' => 'badge bg-info',
            'sales' => 'badge bg-secondary',
            'admin' => 'badge bg-dark'
        ];

        return $classes[$this->department] ?? 'badge bg-light text-dark';
    }
}