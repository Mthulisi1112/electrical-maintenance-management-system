<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'asset_id',
        'performed_by',
        'maintenance_type',
        'actions_taken',
        'measurements',
        'parts_used',
        'time_spent_minutes',
        'observations',
        'attachments',
        'result',
        'next_maintenance_date'
    ];

    protected $casts = [
        'measurements' => 'array',
        'parts_used' => 'array',
        'attachments' => 'array',
        'next_maintenance_date' => 'datetime',
        'time_spent_minutes' => 'integer',
    ];

    /**
     * Get the work order associated with this maintenance log.
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the asset that this maintenance log belongs to.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user who performed the maintenance.
     */
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Scope a query to only include logs of a specific maintenance type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }

    /**
     * Scope a query to only include logs with a specific result.
     */
    public function scopeWithResult($query, $result)
    {
        return $query->where('result', $result);
    }

    /**
     * Scope a query to only include successful maintenance logs.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('result', 'successful');
    }

    /**
     * Scope a query to only include failed maintenance logs.
     */
    public function scopeFailed($query)
    {
        return $query->where('result', 'failed');
    }

    /**
     * Scope a query to only include logs for a specific asset.
     */
    public function scopeForAsset($query, $assetId)
    {
        return $query->where('asset_id', $assetId);
    }

    /**
     * Scope a query to only include logs performed by a specific user.
     */
    public function scopePerformedBy($query, $userId)
    {
        return $query->where('performed_by', $userId);
    }

    /**
     * Scope a query to only include logs within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get the maintenance type label.
     */
    public function getMaintenanceTypeLabelAttribute(): string
    {
        return match($this->maintenance_type) {
            'preventive' => 'Preventive Maintenance',
            'corrective' => 'Corrective Maintenance',
            'inspection' => 'Inspection',
            'calibration' => 'Calibration',
            'repair' => 'Repair',
            default => ucfirst($this->maintenance_type)
        };
    }

    /**
     * Get the result color for badges.
     */
    public function getResultColorAttribute(): string
    {
        return match($this->result) {
            'successful' => 'green',
            'partial' => 'yellow',
            'failed' => 'red',
            'deferred' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get the result label.
     */
    public function getResultLabelAttribute(): string
    {
        return match($this->result) {
            'successful' => 'Successful',
            'partial' => 'Partially Successful',
            'failed' => 'Failed',
            'deferred' => 'Deferred',
            default => ucfirst($this->result)
        };
    }

    /**
     * Get formatted time spent.
     */
    public function getFormattedTimeSpentAttribute(): string
    {
        $hours = floor($this->time_spent_minutes / 60);
        $minutes = $this->time_spent_minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        
        return $minutes . ' minutes';
    }

    /**
     * Get measurements as key-value pairs for easy display.
     */
    public function getMeasurementsListAttribute(): array
    {
        if (!$this->measurements) {
            return [];
        }

        $list = [];
        foreach ($this->measurements as $measurement) {
            $value = $measurement['value'] ?? '';
            $unit = $measurement['unit'] ?? '';
            $list[$measurement['name']] = $value . ($unit ? ' ' . $unit : '');
        }
        
        return $list;
    }

    /**
     * Get parts used as formatted list.
     */
    public function getPartsUsedListAttribute(): array
    {
        if (!$this->parts_used) {
            return [];
        }

        $list = [];
        foreach ($this->parts_used as $part) {
            $list[] = $part['quantity'] . 'x ' . $part['name'] . 
                     (isset($part['part_number']) ? ' (' . $part['part_number'] . ')' : '');
        }
        
        return $list;
    }

    /**
     * Check if next maintenance is overdue.
     */
    public function isNextMaintenanceOverdue(): bool
    {
        return $this->next_maintenance_date && $this->next_maintenance_date->isPast();
    }

    /**
     * Calculate efficiency score based on time spent vs estimated.
     */
    public function getEfficiencyScoreAttribute(): ?float
    {
        if (!$this->workOrder || !$this->workOrder->maintenanceSchedule) {
            return null;
        }

        $estimated = $this->workOrder->maintenanceSchedule->estimated_duration_minutes;
        if ($estimated <= 0) {
            return null;
        }

        $ratio = ($estimated / $this->time_spent_minutes) * 100;
        return min(round($ratio, 2), 100);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            // If asset_id is not set but work_order is, get asset from work order
            if (!$log->asset_id && $log->work_order_id) {
                $workOrder = WorkOrder::find($log->work_order_id);
                if ($workOrder) {
                    $log->asset_id = $workOrder->asset_id;
                }
            }
        });

        static::created(function ($log) {
            // Update the asset's last maintenance date
            if ($log->asset) {
                $log->asset->updated_at = now();
                $log->asset->save();
            }

            // If there's a next maintenance date, update the maintenance schedule
            if ($log->next_maintenance_date && $log->workOrder && $log->workOrder->maintenanceSchedule) {
                $schedule = $log->workOrder->maintenanceSchedule;
                $schedule->last_completed_date = $log->created_at;
                $schedule->next_due_date = $log->next_maintenance_date;
                $schedule->save();
            }
        });
    }
}