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
                    ->options(function () {
                        return \App\Models\Customer::all()
                            ->pluck('name', 'id')
                            ->map(function ($name, $id) {
                                $customer = \App\Models\Customer::find($id);
                                return "{$name} ({$customer->phone})";
                            });
                    })
                    ->getSearchResultsUsing(function (string $query) {
                        return \App\Models\Customer::where('name', 'like', "%{$query}%")
                            ->orWhere('phone', 'like', "%{$query}%")
                            ->get()
                            ->mapWithKeys(function ($customer) {
                                return [$customer->id => "{$customer->name} ({$customer->phone})"];
                            });
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $customer = \App\Models\Customer::find($value);
                        return $customer ? "{$customer->name} ({$customer->phone})" : null;
                    }),
                
                Forms\Components\Select::make('device_type')
                    ->options([
                        'mobile' => 'Mobile Phone',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('device_details', null)),

                Forms\Components\TextInput::make('repair_number')
                    ->hidden()
                    ->default(fn () => strtoupper(Str::random(8))),

                Forms\Components\Fieldset::make('Device Details')
                    ->schema([
                        Forms\Components\TextInput::make('make')
                            ->label('Make')
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
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('repair_number'),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                Tables\Columns\TextColumn::make('device_type')->label('Device Type'),
                Tables\Columns\TextColumn::make('status.name')->label('Status'),
                Tables\Columns\TextColumn::make('location.name')->label('Location'),
            ])
            ->actions([
                Action::make('viewDetails')
                    ->label('View Details')
                    ->modalHeading('Repair Details')
                    ->modalContent(fn ($record) => view('components.repair-details', ['record' => $record]))
                    ->icon('heroicon-o-eye'),

                Action::make('updateStatus')
                    ->label('Update Status')
                    ->modalHeading('Update Repair Status')
                    ->form([
                        Forms\Components\Select::make('status_id')
                            ->label('Status')
                            ->relationship('status', 'name')
                            ->options(RepairStatus::all()->pluck('name', 'id')->toArray()),
                    ])
                    ->action(function ($record, $data) {
                        $record->update(['status_id' => $data['status_id']]);
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
}
