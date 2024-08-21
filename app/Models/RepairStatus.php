<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RepairStatus extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name'];

    // Implement the required getActivitylogOptions method
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name']) // Log changes only to the 'name' attribute
            ->logOnlyDirty()    // Log only when the 'name' attribute is changed
            ->useLogName('repair_status'); // Set a custom log name
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Repair Status record has been {$eventName}";
    }
}
