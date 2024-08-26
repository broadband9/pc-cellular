<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Make extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name']) // Log changes only to the 'name' attribute
            ->logOnlyDirty()    // Log only when the 'name' attribute is changed
            ->useLogName('Device Make'); // Set a custom log name
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Device Make record has been {$eventName}";
    }

}
