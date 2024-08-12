<?php

namespace App\Filament\Resources\RepairResource\Pages;

use App\Filament\Resources\RepairResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRepair extends CreateRecord
{
    protected static string $resource = RepairResource::class;

    protected function afterSave()
    {
        parent::afterSave();

        // Send welcome email after saving a new repair
        Mail::to($this->record->customer->email)->send(new WelcomeEmail($this->record));
    }
}
