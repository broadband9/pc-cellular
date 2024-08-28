<?php

namespace App\Filament\Resources\RepairResource\Pages;

use App\Filament\Resources\RepairResource;
use Filament\Resources\Pages\CreateRecord;
use App\Jobs\SendRepairEmail;
use Illuminate\Support\Facades\Log;

class CreateRepair extends CreateRecord
{
    protected static string $resource = RepairResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
{
    $customer = \App\Models\Customer::find($data['customer_id']);
    $data['email'] = $customer->email;
    return $data;
}

// Ensure `send_email` is part of the saved data


    protected function afterCreate()
    {
        $data = $this->record;
    
        // Dispatch the job to send email
        Log::info('Dispatching SendRepairEmail job', ['data' => $data]);
        SendRepairEmail::dispatch($data);
    }
    

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
