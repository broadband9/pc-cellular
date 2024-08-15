<?php

namespace App\Filament\Resources\RepairResource\Pages;

use App\Filament\Resources\RepairResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;


class CreateRepair extends CreateRecord
{
    protected static string $resource = RepairResource::class;

    protected function afterSave()
{
    parent::afterSave();
    
    \Log::info('afterSave method triggered');
    
    if ($this->record && $this->record->customer && $this->record->customer->email) {
        \Log::info('Customer email: ' . $this->record->customer->email);
        \Mail::to($this->record->customer->email)->send(new WelcomeEmail($this->record));
        \Log::info('Email sent.');
    } else {
        \Log::error('Failed to send email: Invalid email data.');
    }
}

    
}
