<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Repair extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'customer_id', 'device_type', 'repair_number', 'status_id', 'finalized_price', 'location_id',
         'model', 'imei', 'network', 'passcode', 'issue_description', 'estimated_cost', 
        'power_up', 'lens_lcd_damage', 'missing_parts', 'liquid_damage', 'tampered', 
        'button_functions_ok', 'camera_lens_damage', 'sim_sd_removed', 'risk_to_back', 
        'risk_to_lcd', 'risk_to_biometrics','make_id'
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

    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    protected static function booted()
    {
        static::creating(function ($repair) {
            // Generate a unique 3-character code with the "ezy" prefix
            do {
                // Generate a random 3-character string (e.g., 'A1B', 'C3D')
                $randomCode = strtoupper(Str::random(3));
                
                // Combine the "ezy" prefix with the random code
                $repairNumber = 'ezy' . $randomCode;

                // Ensure the generated repair number is unique in the database
                $exists = Repair::where('repair_number', $repairNumber)->exists();
            } while ($exists);

            // Fetch the customer's name or default to 'Unknown'
            $customerName = $repair->customer ? $repair->customer->name : 'Unknown';
        
            // Use "nopin" if the passcode is not provided
            $passcode = $repair->passcode ?: 'nopin';

            // Format the repair number: {ezy + 3-character code} - {Today's Date} - {Name} - {passcode/nopin}
            $repair->repair_number = sprintf(
                '%s-%s-%s-%s',
                $repairNumber,
                now()->format('d/m/y'),
                Str::slug($customerName),
                $passcode
            );
        });
    }

    

    // Implement the required getActivitylogOptions method
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'customer_id', 'device_type', 'repair_number', 'status_id', 'finalized_price', 
                'location_id', 'make_id', 'model', 'imei', 'network', 'passcode', 'issue_description', 
                'estimated_cost', 'power_up', 'lens_lcd_damage', 'missing_parts', 'liquid_damage', 
                'tampered', 'button_functions_ok', 'camera_lens_damage', 'sim_sd_removed', 
                'risk_to_back', 'risk_to_lcd', 'risk_to_biometrics'
            ]) // Log changes to these attributes
            ->logOnlyDirty() // Log only when these attributes are changed
            ->useLogName('repair'); // Set a custom log name
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Repair record has been {$eventName}";
    }
}
