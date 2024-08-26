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

                    Forms\Components\TextInput::make('email')
                    ->label('Customer Email')
                    ->hidden() // Hide this field from the form
                    ->default(fn ($state) => $state),
                
                    Forms\Components\Select::make('device_type')
                    ->options([
                        'mobile' => 'Mobile Phone',
                    ])
                    ->default('mobile')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('device_details', null)),

                    Forms\Components\TextInput::make('repair_number')
                    ->hidden()
                    ->default(fn () => strtoupper(Str::random(3))),

                Forms\Components\Fieldset::make('Device Details')
                    ->schema([
                        Forms\Components\Select::make('make_id')
                        ->label('Make')
                        ->relationship('make', 'name')
                        ->searchable()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->label('Make Name')
                                ->required(),
                        ])
                        ->options(function () {
                            return \App\Models\Make::all()
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->getOptionLabelUsing(function ($value) {
                            $make = \App\Models\Make::find($value);
                            return $make ? $make->name : null;
                        })
                        ->visible(fn ($get) => $get('device_type') === 'mobile')
                        ->nullable(), // Ensure this field is optional
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
                        Forms\Components\Textarea::make('issue_description')
                            ->label('Issue Description / Notes')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\TextInput::make('estimated_cost')
                            ->label('Estimated Cost')
                            ->numeric()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        
                        // Mobile-specific boolean fields
                        Forms\Components\Checkbox::make('power_up')
                            ->label('Power Up')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\Checkbox::make('lens_lcd_damage')
                            ->label('Lens / LCD Damage')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\Checkbox::make('missing_parts')
                            ->label('Missing Parts')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\Checkbox::make('liquid_damage')
                            ->label('Liquid Damage')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\Checkbox::make('tampered')
                            ->label('Tampered')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\Checkbox::make('button_functions_ok')
                            ->label('Button Functions OK')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\Checkbox::make('camera_lens_damage')
                            ->label('Camera Lens / Back Damage')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\Checkbox::make('sim_sd_removed')
                            ->label('SIM and SD Removed')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\Checkbox::make('risk_to_back')
                            ->label('Risk to Back')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\Checkbox::make('risk_to_lcd')
                            ->label('Risk to LCD')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\Checkbox::make('risk_to_biometrics')
                            ->label('Risk to Biometrics')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                    ]),
                
                Forms\Components\Select::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Status Name')
                            ->required(),
                    ])
                    ->options(RepairStatus::all()->pluck('name', 'id')->toArray()),

                Forms\Components\Select::make('location_id')
                    ->label('Location')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Location Name')
                            ->required(),
                    ])
                    ->options(Location::all()->pluck('name', 'id')->toArray()),

                Forms\Components\TextInput::make('finalized_price')
                    ->label('Finalized Price')
                    ->numeric(),

                // Toggle for sending email
                Forms\Components\Toggle::make('send_email')
                    ->label('Send Email Notification')
                    ->default(false)
                    ->reactive(),

                // Email message box
                Forms\Components\Textarea::make('email_message')
                    ->label('Custom Email Message')
                    ->placeholder('Enter your message here...')
                    ->visible(fn ($get) => $get('send_email'))
                    ->required(fn ($get) => $get('send_email')),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('repair_number')
                ->label('Repair Number')
                ->searchable(),

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
            Action::make('updateRepair')
    ->label('Update Repair')
    ->modalHeading('Update Repair Details')
    ->form([
        Forms\Components\Select::make('status_id')
            ->label('Status')
            ->relationship('status', 'name')
            ->options(RepairStatus::all()->pluck('name', 'id')->toArray()),

        Forms\Components\Select::make('location_id')
            ->label('Location')
            ->relationship('location', 'name')
            ->options(Location::all()->pluck('name', 'id')->toArray()),

        Forms\Components\Textarea::make('note')
            ->label('Note')
            ->required(),

        Forms\Components\TextInput::make('finalized_price')
            ->label('Finalized Price')
            ->numeric()
            ->required(),
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
    protected static function afterCreate($record)
    {
        if ($record->send_email) {
            $message = $record->email_message;
            Mail::to($record->customer->email)->send(new RepairReadyForPickup($message));
        }
    }
}
