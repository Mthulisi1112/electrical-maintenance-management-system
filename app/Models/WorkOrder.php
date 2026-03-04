<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_number', 'asset_id', 'maintenance_schedule_id',
        'technician_id', 'supervisor_id', 'type', 'status', 'title',
        'description', 'checklist', 'checklist_responses', 'scheduled_date',
        'started_at', 'completed_date', 'verified_at', 'time_spent_minutes',
        'parts_used', 'technician_remarks', 'supervisor_remarks'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_at' => 'datetime',
        'completed_date' => 'datetime',
        'verified_at' => 'datetime',
        'checklist' => 'array',
        'checklist_responses' => 'array',
        'parts_used' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($workOrder) {
            $workOrder->work_order_number = 'WO-' . date('Ymd') . '-' . str_pad(static::max('id') + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function maintenanceSchedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function maintenanceLog()
    {
        return $this->hasOne(MaintenanceLog::class);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            'verified' => 'purple',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function fault()
    {
        return $this->belongsTo(Fault::class);
    }
    
}