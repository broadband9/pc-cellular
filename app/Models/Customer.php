<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Customer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'postcode',
    ];

    // Implement the required getActivitylogOptions method
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'phone', 'email', 'postcode'])
            ->logOnlyDirty()
            ->useLogName('customer');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Customer record has been {$eventName}";
    }
}
