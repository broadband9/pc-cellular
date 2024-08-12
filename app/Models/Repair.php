<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'device_type', 'repair_number', 'status', 'finalized_price', 'quoted_price', 'location',
        'make', 'model', 'imei', 'network', 'passcode', 'issue_description', 'estimated_cost', 
        'power_up', 'lens_lcd_damage', 'missing_parts', 'liquid_damage', 'tampered', 
        'button_functions_ok', 'camera_lens_damage', 'sim_sd_removed', 'risk_to_back', 
        'risk_to_lcd', 'risk_to_biometrics', 'laptop_make', 'laptop_model', 
        'laptop_serial_number', 'laptop_os', 'laptop_warranty', 'laptop_issue_description',
        'laptop_estimated_cost', 'laptop_location'
        // Add other device-specific fields as needed
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

