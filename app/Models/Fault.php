<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Fault extends Model
{
    use HasFactory;

    protected $fillable = [
        'fault_number',
        'asset_id',
        'reported_by',
        'assigned_to',
        'fault_type',
        'severity',
        'status',
        'description',
        'symptoms',
        'images',
        'downtime_start',
        'downtime_end',
        'downtime_minutes',
        'root_cause',
        'corrective_actions',
        'parts_replaced',
        'requires_followup'
    ];

    protected $casts = [
        'symptoms' => 'array',
        'images' => 'array',
        'parts_replaced' => 'array',
        'downtime_start' => 'datetime',
        'downtime_end' => 'datetime',
        'requires_followup' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($fault) {
            $fault->fault_number = 'FLT-' . date('Ymd') . '-' . str_pad(static::max('id') + 1, 4, '0', STR_PAD_LEFT);
        });

        static::saving(function ($fault) {
            if ($fault->downtime_start && $fault->downtime_end) {
                $fault->downtime_minutes = $fault->downtime_start->diffInMinutes($fault->downtime_end);
            }
        });
    }

    /**
     * Get the asset that this fault belongs to.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user who reported this fault.
     */
    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the user assigned to this fault.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the work orders created for this fault.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Scope a query to only include faults with a specific severity.
     */
    public function scopeSeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope a query to only include faults with a specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include critical faults.
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical')->whereNotIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope a query to only include unresolved faults.
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNotIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope a query to only include faults requiring follow-up.
     */
    public function scopeRequiresFollowup($query)
    {
        return $query->where('requires_followup', true);
    }

    /**
     * Assign this fault to a technician.
     */
    public function assignTo(User $technician): self
    {
        $this->assigned_to = $technician->id;
        $this->status = 'investigating';
        $this->save();
        
        return $this;
    }

    /**
     * Resolve this fault.
     */
    public function resolve(string $rootCause, string $correctiveActions, ?array $partsReplaced = null): self
    {
        $this->root_cause = $rootCause;
        $this->corrective_actions = $correctiveActions;
        $this->parts_replaced = $partsReplaced;
        $this->status = 'resolved';
        $this->downtime_end = now();
        $this->save();
        
        return $this;
    }

    /**
     * Close this fault.
     */
    public function close(): self
    {
        $this->status = 'closed';
        $this->save();
        
        return $this;
    }

    /**
     * Check if the fault is resolved.
     */
    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Get the severity color for badges.
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get the status color for badges.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'reported' => 'yellow',
            'investigating' => 'blue',
            'in_progress' => 'indigo',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get the fault type label.
     */
    public function getFaultTypeLabelAttribute(): string
    {
        return match($this->fault_type) {
            'trip' => 'Trip',
            'overload' => 'Overload',
            'short_circuit' => 'Short Circuit',
            'earth_fault' => 'Earth Fault',
            'overheating' => 'Overheating',
            'mechanical' => 'Mechanical',
            'other' => 'Other',
            default => ucfirst($this->fault_type)
        };
    }

    /**
     * Calculate total downtime in hours.
     */
    public function getDowntimeHoursAttribute(): ?float
    {
        return $this->downtime_minutes ? round($this->downtime_minutes / 60, 2) : null;
    }

    /**
     * Format downtime duration for display.
     */
    public function getDowntimeFormattedAttribute(): string
    {
        if (!$this->downtime_minutes) {
            return 'N/A';
        }
        
        $hours = floor($this->downtime_minutes / 60);
        $minutes = $this->downtime_minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        
        return $minutes . ' minutes';
    }
}