<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_code', 'type', 'name', 'location', 'manufacturer', 'model',
        'serial_number', 'voltage_rating', 'current_rating', 'power_rating',
        'installation_date', 'status', 'technical_specs', 'qr_code', 'created_by'
    ];

    protected $casts = [
        'installation_date' => 'date',
        'technical_specs' => 'array',
        'voltage_rating' => 'decimal:2',
        'current_rating' => 'decimal:2',
        'power_rating' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function faults()
    {
        return $this->hasMany(Fault::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'operational' => 'green',
            'maintenance' => 'yellow',
            'faulty' => 'red',
            'decommissioned' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Generate a unique QR code for the asset
     */
    public function generateQrCode()
    {
        $this->qr_code = 'ASSET-' . $this->id . '-' . uniqid();
        $this->save();
        
        return $this->qr_code;
    }

    /**
     * Get the QR code URL
     */
    public function getQrCodeUrlAttribute()
    {
        return route('assets.qrcode', $this);
    }
}