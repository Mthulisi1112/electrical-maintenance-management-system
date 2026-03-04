<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'frequency',
        'title',
        'description',
        'checklist_items',
        'required_tools',
        'estimated_duration_minutes',
        'start_date',
        'next_due_date',
        'last_completed_date',
        'is_active',
        'priority',
        'created_by'
    ];

    protected $casts = [
        'checklist_items' => 'array',
        'required_tools' => 'array',
        'start_date' => 'date',
        'next_due_date' => 'datetime',
        'last_completed_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the asset that this maintenance schedule belongs to.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user who created this maintenance schedule.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the work orders generated from this schedule.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Scope a query to only include active schedules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include schedules due by a specific date.
     */
    public function scopeDueBy($query, $date)
    {
        return $query->where('next_due_date', '<=', $date);
    }

    /**
     * Scope a query to only include schedules with a specific priority.
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Calculate the next due date based on frequency.
     */
    public function calculateNextDueDate(): \Carbon\Carbon
    {
        $lastDate = $this->last_completed_date ?? $this->start_date;
        
        return match($this->frequency) {
            'daily' => \Carbon\Carbon::parse($lastDate)->addDay(),
            'weekly' => \Carbon\Carbon::parse($lastDate)->addWeek(),
            'monthly' => \Carbon\Carbon::parse($lastDate)->addMonth(),
            'quarterly' => \Carbon\Carbon::parse($lastDate)->addMonths(3),
            'semi_annual' => \Carbon\Carbon::parse($lastDate)->addMonths(6),
            'annual' => \Carbon\Carbon::parse($lastDate)->addYear(),
            default => \Carbon\Carbon::parse($lastDate)->addMonth(),
        };
    }

    /**
     * Update the next due date after completion.
     */
    public function updateNextDueDate(): void
    {
        $this->last_completed_date = now();
        $this->next_due_date = $this->calculateNextDueDate();
        $this->save();
    }

    /**
     * Check if the schedule is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->is_active && $this->next_due_date->isPast();
    }

    /**
     * Get the priority color for badges.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get the frequency label.
     */
    public function getFrequencyLabelAttribute(): string
    {
        return match($this->frequency) {
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'semi_annual' => 'Semi-Annual',
            'annual' => 'Annual',
            default => ucfirst($this->frequency)
        };
    }
}