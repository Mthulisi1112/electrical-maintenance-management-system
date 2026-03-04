<?php

namespace App\Models;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'employee_id',
        'department', 'phone', 'avatar', 'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role)
    {
         
        if (!$this->role) {
            return false;
        }

        // If $role is an array, check if the user's role slug is in that array
        if (is_array($role)) {
            return in_array($this->role->slug, $role);
        }

        // Check if the user's role slug matches the provided role
        return $this->role->slug === $role;
    }

    public function hasPermission($permission)
    {
        $permissions = json_decode($this->role?->permissions ?? '[]', true);
        return in_array($permission, $permissions);
    }

    public function assignedWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'technician_id');
    }

    public function supervisedWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'supervisor_id');
    }

    public function reportedFaults()
    {
        return $this->hasMany(Fault::class, 'reported_by');
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class, 'performed_by');
    }
}