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
use Illuminate\Support\Facades\Mail;
use App\Mail\RepairReadyForPickup;
use App\Mail\RepairAwaitingParts;
use App\Mail\RepairAwaitingCustomer;
use Illuminate\Support\Collection;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Tables\Columns\ImageColumn;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Log;

class RepairResource extends Resource
{
    protected static ?string $model = Repair::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Customer selection
                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'name')
                    ->required()
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Customer Name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number'),
                        Forms\Components\TextInput::make('postcode')
                            ->label('Postcode'),
                    ])
                    ->options(function () {
                        return \App\Models\Customer::all()
                            ->pluck('name', 'id')
                            ->map(function ($name, $id) {
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
    
                // Device type selection
                Forms\Components\Select::make('device_type')
                    ->label('Device Type')
                    ->options([
                        'mobile' => 'Mobile Phone',
                    ])
                    ->default('mobile')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('device_details', null)),
    
                // Device Details Fieldset
                Forms\Components\Fieldset::make('Device Details')
                    ->schema([
                        Forms\Components\Select::make('make_id')
                            ->label('Make')
                            ->relationship('make', 'name')
                            ->options(function () {
                                return \App\Models\Make::all()->pluck('name', 'id');
                            })
                            ->searchable()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\TextInput::make('model')
                            ->label('Model')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\TextInput::make('imei')
                            ->label('IMEI')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Select::make('network')
                            ->label('Network')
                            ->options([
                                'O2' => 'O2',
                                'Vodafone' => 'Vodafone',
                                'EE' => 'EE',
                                'Three' => 'Three',
                                'BT' => 'BT',
                                'Giffgaff' => 'Giffgaff',
                                'Tesco Mobile' => 'Tesco Mobile',
                                'Sky Mobile' => 'Sky Mobile',
                                'Virgin Mobile' => 'Virgin Mobile',
                                'Other' => 'Other',
                            ])
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\TextInput::make('passcode')
                            ->label('Passcode')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\TextInput::make('estimated_cost')
                            ->label('Estimated Cost')
                            ->numeric()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Textarea::make('issue_description')
                            ->label('Issue Description / Notes')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                    ])
                    ->columns(2), // Grouping fields into two columns
    
                // Mobile-specific boolean fields with inline radio buttons for Yes/No
                Forms\Components\Fieldset::make('Device Condition')
                    ->schema([
                        Forms\Components\Radio::make('power_up')
                            ->label('Power Up')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Radio::make('lens_lcd_damage')
                            ->label('Lens / LCD Damage')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Radio::make('missing_parts')
                            ->label('Missing Parts')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Radio::make('liquid_damage')
                            ->label('Liquid Damage')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Radio::make('tampered')
                            ->label('Tampered')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Radio::make('button_functions_ok')
                            ->label('Button Functions OK')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Radio::make('camera_lens_damage')
                            ->label('Camera Lens / Back Damage')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Radio::make('sim_sd_removed')
                            ->label('SIM and SD Removed')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Radio::make('risk_to_back')
                            ->label('Risk to Back')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Radio::make('risk_to_lcd')
                            ->label('Risk to LCD')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
    
                        Forms\Components\Radio::make('risk_to_biometrics')
                            ->label('Risk to Biometrics')
                            ->options([true => 'Yes', false => 'No'])
                            ->inline()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                    ])
                    ->columns(2), // Grouping fields into two columns
    
                // Status and Location Selection
                Forms\Components\Fieldset::make('Repair Details')
                    ->schema([
                        Forms\Components\Select::make('status_id')
                            ->label('Status')
                            ->relationship('status', 'name')
                            ->options(function () {
                                return \App\Models\RepairStatus::all()->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
    
                            Forms\Components\Select::make('location_id')
                            ->label('Location')
                            ->relationship('location', 'name')
                            ->options(function () {
                                return \App\Models\Location::all()->pluck('name', 'id');
                            })
                            ->searchable(),
    
                        Forms\Components\TextInput::make('finalized_price')
                            ->label('Finalized Price')
                            ->numeric(),
                    ])
                    ->columns(1), // Align fields vertically
    
                // Email Notification Toggle and Message
                Forms\Components\Toggle::make('send_email')
                ->label('Send Email Notification')
                ->default(true)
                ->reactive(),
            
            Forms\Components\Textarea::make('email_message')
                ->label('Custom Email Message')
                ->placeholder('Enter your message here...')
                ->visible(fn ($get) => $get('send_email'))
                ->required(fn ($get) => $get('send_email')),
            
            
    
                // New Custom Signature Pad component
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
                Action::make('viewDetails')
                ->label('View Details')
                ->modalHeading('Repair Details')
                ->modalContent(fn ($record) => view('components.repair-details', ['record' => $record]))
                ->icon('heroicon-o-eye'),
                Action::make('updateRepair')
                ->label('Update Repair')
                ->modalHeading('Update Repair Details')
                ->form(fn ($record) => [
                    Forms\Components\Select::make('status_id')
                        ->label('Status')
                        ->relationship('status', 'name')
                        ->options(RepairStatus::all()->pluck('name', 'id')->toArray())
                        ->default($record->status_id) // Pre-fill with current value
                        ->required(),
            
                    Forms\Components\Select::make('location_id')
                        ->label('Location')
                        ->relationship('location', 'name')
                        ->options(Location::all()->pluck('name', 'id')->toArray())
                        ->default($record->location_id) // Pre-fill with current value
                        ->required(),
            
                    Forms\Components\TextInput::make('finalized_price')
                        ->label('Finalized Price')
                        ->numeric()
                        ->default($record->finalized_price) // Pre-fill with current value
                        ->required(),

                        Forms\Components\Textarea::make('note')
                        ->label('Note')
                        ->default($record->note),
                ])
                ->action(function ($record, $data) {
                    // Fetch existing data
                    $existingData = $record->only(['status_id', 'location_id', 'note', 'finalized_price']);
            
                    // Update the record
                    $record->update([
                        'status_id' => $data['status_id'],
                        'location_id' => $data['location_id'],
                        'note' => $data['note'],
                        'finalized_price' => $data['finalized_price'],
                    ]);
            
                    // Determine which fields have changed
                    $changes = [];
                    foreach ($data as $key => $value) {
                        if ($existingData[$key] !== $value) {
                            $changes[$key] = [
                                'old' => $existingData[$key],
                                'new' => $value,
                            ];
                        }
                    }
            
                    // Create a detailed log message
                    $logMessage = 'Updated repair details: ';
                    foreach ($changes as $field => $change) {
                        $logMessage .= sprintf(
                            '%s changed from "%s" to "%s". ',
                            ucfirst(str_replace('_', ' ', $field)),
                            $change['old'],
                            $change['new']
                        );
                    }
            
                    // Log the activity
                    activity()
                        ->performedOn($record)
                        ->causedBy(auth()->user())
                        ->withProperties([
                            'changes' => $changes
                        ])
                        ->log($logMessage);
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
    protected function afterCreate()
    {
        $data = $this->record;
    
        // Dispatch the job to send email
        Log::info('Dispatching SendRepairEmail job', ['data' => $data]);
        SendRepairEmail::dispatch($data);
    }
    
    

}
