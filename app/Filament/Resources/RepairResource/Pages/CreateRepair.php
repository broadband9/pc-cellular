<?php

namespace App\Filament\Resources\RepairResource\Pages;

use App\Filament\Resources\RepairResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Log;

class CreateRepair extends CreateRecord
{
    protected static string $resource = RepairResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
{
    $customer = \App\Models\Customer::find($data['customer_id']);
    $data['email'] = $customer->email;
    Log::info('Mutated form data before create:', ['data' => $data]);
    return $data;
}

protected function afterCreate()
{
    $data = $this->record;

    Log::info('After create method triggered.', ['data' => $data]);

    if ($data->send_email && $data->email) {
        Log::info('Email toggle is on and email is set:', ['email' => $data->email]);
        try {
            Log::info('Attempting to send email to: ' . $data->email);
            Mail::to($data->email)->send(new WelcomeEmail($data));
            Log::info('Email sent successfully to: ' . $data->email);
        } catch (\Swift_TransportException $e) {
            Log::error('Failed to send email (transport exception): ' . $e->getMessage());
        } catch (\Swift_RfcComplianceException $e) {
            Log::error('Failed to send email (RFC compliance exception): ' . $e->getMessage());
        } catch (\Swift_IoException $e) {
            Log::error('Failed to send email (I/O exception): ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Failed to send email (general exception): ' . $e->getMessage());
        }
    } else {
        Log::info('Email not sent as the toggle is off or the email is not set');
    }
}
}
