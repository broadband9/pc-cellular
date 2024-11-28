<?php

namespace App\Filament\Resources\RepairResource\Pages;

use App\Filament\Resources\RepairResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomRepairEmail;
use Illuminate\Support\Facades\Log;

class CreateRepair extends CreateRecord
{
    protected static string $resource = RepairResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check if send_email is true and add logic to send email
        if (isset($data['send_email']) && $data['send_email']) {
            // This flag will be used in the afterCreate hook
            $data['should_send_email'] = true;
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Check if the email should be sent
        if ($record->send_email) {
            try {
                $customer = $record->customer;

                if ($customer && $customer->email) {
                    Mail::to($customer->email)->send(
                        new CustomRepairEmail($record)
                    );

                    Log::info('Repair notification email sent', [
                        'repair_id' => $record->id,
                        'customer_email' => $customer->email
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send repair notification email', [
                    'repair_id' => $record->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}