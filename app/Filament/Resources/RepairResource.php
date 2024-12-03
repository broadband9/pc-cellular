<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepairResource\Pages;
use App\Models\Repair;
use App\Models\RepairStatus;
use App\Models\Location;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Str;
use App\Mail\RepairReadyForPickup;
use App\Mail\RepairAwaitingParts;
use App\Mail\RepairAwaitingCustomer;
use Illuminate\Support\Collection;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Tables\Columns\ImageColumn;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Log;
use App\Services\CupsService;
use App\Mail\CustomRepairEmail;
use Illuminate\Support\Facades\Mail;

class RepairResource extends Resource
{

    
    protected static ?string $model = Repair::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'name')
                    ->required()
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('email')->email(),
                        Forms\Components\TextInput::make('phone'),
                        Forms\Components\TextInput::make('postcode'),
                    ])
                    ->options(function () {
                        return \App\Models\Customer::all()->pluck('name', 'id')->map(function ($name, $id) {
                            $customer = \App\Models\Customer::find($id);
                            return "{$name} ({$customer->phone}, {$customer->email})";
                        });
                    })
                    ->getSearchResultsUsing(function (string $query) {
                        return \App\Models\Customer::where('name', 'like', "%{$query}%")
                            ->orWhere('phone', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%")
                            ->get()
                            ->mapWithKeys(function ($customer) {
                                return [$customer->id => "{$customer->name} ({$customer->phone}, {$customer->email})"];
                            });
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $customer = \App\Models\Customer::find($value);
                        return $customer ? "{$customer->name} ({$customer->phone}, {$customer->email})" : null;
                    }),
    
                Forms\Components\TextInput::make('repair_number')
                    ->hidden()
                    ->default(fn () => strtoupper(Str::random(3))),
    
                Forms\Components\Select::make('device_type')
                    ->label('Device Type')
                    ->options([
                        'mobile' => 'Mobile Phone',
                        'tablet' => 'Tablet',
                        'laptop' => 'Laptop',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('device_details', null)),
    
                Forms\Components\Fieldset::make('Device Details')
                    ->schema([
                        Forms\Components\Select::make('make_id')
                            ->label('Make')
                            ->relationship('make', 'name')
                            ->options(fn () => \App\Models\Make::all()->pluck('name', 'id'))
                            ->searchable(),
    
                        Forms\Components\TextInput::make('model')->label('Model'),
                        Forms\Components\TextInput::make('imei')
                            ->label('IMEI')
                            ->visible(fn ($get) => in_array($get('device_type'), ['mobile', 'tablet'])),
    
                        Forms\Components\Select::make('network')
                            ->label('Network')
                            ->options([
                                'O2' => 'O2', 'Vodafone' => 'Vodafone', 'EE' => 'EE', 'Three' => 'Three',
                                'BT' => 'BT', 'Giffgaff' => 'Giffgaff', 'Tesco Mobile' => 'Tesco Mobile',
                                'Sky Mobile' => 'Sky Mobile', 'Virgin Mobile' => 'Virgin Mobile', 'Other' => 'Other',
                            ])
                            ->visible(fn ($get) => in_array($get('device_type'), ['mobile', 'tablet'])),
    
                        Forms\Components\TextInput::make('passcode')->label('Passcode'),
                        Forms\Components\TextInput::make('estimated_cost')
                            ->label('Estimated Cost')
                            ->numeric(),
    
                        Forms\Components\Textarea::make('issue_description')
                            ->label('Issue Description / Notes'),
    
                        // Laptop-specific fields
                        Forms\Components\TextInput::make('operating_system')
                            ->label('Operating System')
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
    
                        Forms\Components\TextInput::make('ram')
                            ->label('RAM')
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
    
                        Forms\Components\TextInput::make('storage')
                            ->label('Storage')
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
                    ])
                    ->columns(2),
    
                Forms\Components\Fieldset::make('Device Condition')
                    ->schema([
                        // Common fields for all device types
                        Forms\Components\Radio::make('power_up')
                            ->label('Power Up')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline(),
    
                        Forms\Components\Radio::make('missing_parts')
                            ->label('Missing Parts')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline(),
    
                        Forms\Components\Radio::make('liquid_damage')
                            ->label('Liquid Damage')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline(),
    
                        Forms\Components\Radio::make('tampered')
                            ->label('Tampered')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline(),
    
                        // Mobile and Tablet specific fields
                        Forms\Components\Radio::make('lens_lcd_damage')
                            ->label('Lens / LCD Damage')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => in_array($get('device_type'), ['mobile', 'tablet'])),
    
                        Forms\Components\Radio::make('button_functions_ok')
                            ->label('Button Functions OK')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => in_array($get('device_type'), ['mobile', 'tablet'])),
    
                        Forms\Components\Radio::make('camera_lens_damage')
                            ->label('Camera Lens / Back Damage')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => in_array($get('device_type'), ['mobile', 'tablet'])),
    
                        Forms\Components\Radio::make('sim_sd_removed')
                            ->label('SIM and SD Removed')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => in_array($get('device_type'), ['mobile', 'tablet'])),
    
                        Forms\Components\Radio::make('risk_to_back')
                            ->label('Risk to Back')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => in_array($get('device_type'), ['mobile', 'tablet'])),
    
                        Forms\Components\Radio::make('risk_to_lcd')
                            ->label('Risk to LCD')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => in_array($get('device_type'), ['mobile', 'tablet'])),
    
                        Forms\Components\Radio::make('risk_to_biometrics')
                            ->label('Risk to Biometrics')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => in_array($get('device_type'), ['mobile', 'tablet'])),
    
                        // Laptop specific fields
                        Forms\Components\Radio::make('keyboard_functional')
                            ->label('Keyboard Functional')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
    
                        Forms\Components\Radio::make('trackpad_functional')
                            ->label('Trackpad Functional')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
    
                        Forms\Components\Radio::make('screen_damage')
                            ->label('Screen Damage')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
    
                        Forms\Components\Radio::make('hinge_damage')
                            ->label('Hinge Damage')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
                    ])
                    ->columns(2),
    
                Forms\Components\Fieldset::make('Repair Details')
                    ->schema([
                        Forms\Components\Select::make('status_id')
                            ->label('Status')
                            ->relationship('status', 'name')
                            ->options(fn () => \App\Models\RepairStatus::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
    
                        Forms\Components\Select::make('location_id')
                            ->label('Location')
                            ->relationship('location', 'name')
                            ->options(fn () => \App\Models\Location::all()->pluck('name', 'id'))
                            ->searchable(),
    
                        Forms\Components\TextInput::make('finalized_price')
                            ->label('Finalized Price')
                            ->numeric(),
                    ])
                    ->columns(1),
    
                    Forms\Components\Toggle::make('send_email')
                    ->label('Send Email Notification')
                    ->default(false),
    
    
                Forms\Components\TextInput::make('customer_signature')
                    ->hidden()
                    ->default(fn () => null),
    
                SignaturePad::make('customer_signature')
                    ->clearable(true)
                    ->downloadable(true)
                    ->undoable(true)
                    ->confirmable(true),
            ]);
    }
    


    public static function table(Tables\Table $table): Tables\Table
    {
        // Retrieve printers using CupsService
        $printers = app(CupsService::class)->listPrinters();
    
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('repair_number')
                    ->label('Repair Number')
                    ->searchable(),
                ImageColumn::make('customer_signature'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_type')
                    ->label('Device Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->label('Status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Note')
                    ->searchable(),
                Tables\Columns\TextColumn::make('finalized_price')
                    ->label('Finalized Price')
                    ->searchable(),
            ])
            ->filters([
                // Dropdown filter for Location
                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Location')
                    ->relationship('location', 'name')
                    ->options(Location::all()->pluck('name', 'id')->toArray()),
    
                // Dropdown filter for Repair Status
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->options(RepairStatus::all()->pluck('name', 'id')->toArray()),
            ])
            ->actions([

                Action::make('printLabel')
                ->label('Print Label')
                ->url(function ($record) {
                    $url = route('repair-label', ['repair' => $record->id]);
                    Log::info("Generated Print Label URL: " . $url); // Log the generated URL
                    return $url;
                })
                ->icon('heroicon-o-printer')
                ->openUrlInNewTab(),
            
                // View Details Action
                Action::make('viewDetails')
                    ->label('View Details')
                    ->modalHeading('Repair Details')
                    ->modalContent(fn($record) => view('components.repair-details', [
                        'record' => $record, // Pass the record to the modal view
                    ]))
                    ->icon('heroicon-o-eye'),
    
                // Update Repair Action
                Action::make('updateRepair')
                    ->label('Update Repair')
                    ->modalHeading('Update Repair Details')
                    ->form(fn($record) => [
                        Forms\Components\Select::make('status_id')
                            ->label('Status')
                            ->relationship('status', 'name')
                            ->options(RepairStatus::all()->pluck('name', 'id')->toArray())
                            ->default($record->status_id)
                            ->required(),
                        Forms\Components\Select::make('location_id')
                            ->label('Location')
                            ->relationship('location', 'name')
                            ->options(Location::all()->pluck('name', 'id')->toArray())
                            ->default($record->location_id)
                            ->required(),
                        Forms\Components\TextInput::make('finalized_price')
                            ->label('Finalized Price')
                            ->numeric()
                            ->default($record->finalized_price)
                            ->required(),
                        Forms\Components\Textarea::make('note')
                            ->label('Note')
                            ->default($record->note),
                    ])
                    ->action(function ($record, $data) {
                        // Capture the old data before updating
                        $existingData = $record->only(['status_id', 'location_id', 'note', 'finalized_price']);
    
                        // Update the record with new data
                        $record->update([
                            'status_id' => $data['status_id'],
                            'location_id' => $data['location_id'],
                            'note' => $data['note'],
                            'finalized_price' => $data['finalized_price'],
                        ]);
    
                        // Check which fields were changed
                        $changes = [];
                        foreach ($data as $key => $value) {
                            if ($existingData[$key] !== $value) {
                                $changes[$key] = [
                                    'old' => $existingData[$key],
                                    'new' => $value,
                                ];
                            }
                        }
    
                        // Generate a log message for changes
                        $logMessage = 'Updated repair details: ';
                        foreach ($changes as $field => $change) {
                            $logMessage .= sprintf(
                                '%s changed from "%s" to "%s". ',
                                ucfirst(str_replace('_', ' ', $field)),
                                $change['old'] ?? 'N/A',
                                $change['new'] ?? 'N/A'
                            );
                        }
    
                        // Log the activity
                        activity()
                            ->performedOn($record)
                            ->causedBy(auth()->user())
                            ->withProperties(['changes' => $changes])
                            ->log($logMessage);
    
                        // Send email if status changes to specific values
                       // Email sending logic in the action method
try {
    $customer = $record->customer;
    if ($customer && $customer->email) {
        $status = RepairStatus::find($data['status_id']);
        if ($status) {
            switch ($status->name) {
                case 'Ready for Pickup':
                    Mail::to($customer->email)->send(new RepairReadyForPickup($record));
                    Log::info('Ready for Pickup email sent', [
                        'repair_id' => $record->id,
                        'customer_email' => $customer->email
                    ]);
                    break;
                case 'Awaiting Parts':
                    Mail::to($customer->email)->send(new RepairAwaitingParts($record));
                    Log::info('Awaiting Parts email sent', [
                        'repair_id' => $record->id,
                        'customer_email' => $customer->email
                    ]);
                    break;
                case 'Awaiting Customer':
                    Mail::to($customer->email)->send(new RepairAwaitingCustomer($record));
                    Log::info('Awaiting Customer email sent', [
                        'repair_id' => $record->id,
                        'customer_email' => $customer->email
                    ]);
                    break;
            }
        }
    }
} catch (\Exception $e) {
    Log::error('Failed to send status update email', [
        'repair_id' => $record->id,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
                    })
                    ->icon('heroicon-o-pencil'),
            ]);
    }



    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRepairs::route('/'),
            'create' => Pages\CreateRepair::route('/create'),
            'edit' => Pages\EditRepair::route('/{record}/edit'),
        ];
    }

    // Optionally, you can handle sending email in a custom save method
    public function afterCreate(): void
    {
        $record = $this->record;
        
        // Check if send_email is true
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

    // Similar method for edit page if needed
    public function afterUpdate(): void
    {
        $record = $this->record;
        
        // Similar logic to afterCreate if you want to send emails on updates
        if ($record->send_email) {
            // Email sending logic
        }
    }
    
    

}
