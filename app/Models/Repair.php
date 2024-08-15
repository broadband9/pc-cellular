<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Repair extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'device_type', 'repair_number', 'status_id', 'finalized_price', 'location_id',
        'make', 'model', 'imei', 'network', 'passcode', 'issue_description', 'estimated_cost', 
        'power_up', 'lens_lcd_damage', 'missing_parts', 'liquid_damage', 'tampered', 
        'button_functions_ok', 'camera_lens_damage', 'sim_sd_removed', 'risk_to_back', 
        'risk_to_lcd', 'risk_to_biometrics'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function status()
    {
        return $this->belongsTo(RepairStatus::class, 'status_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    protected static function booted()
    {
        static::creating(function ($repair) {
            if (empty($repair->repair_number)) {
                $repair->repair_number = strtoupper(Str::random(8));
            }
        });
    }
}

