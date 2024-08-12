<?php

namespace App\Filament\Resources;

namespace App\Filament\Resources;

use App\Filament\Resources\RepairResource\Pages;
use App\Models\Repair;
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
                        Forms\Components\TextInput::make('network')
                            ->label('Network')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\TextInput::make('passcode')
                            ->label('Passcode')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\TextArea::make('issue_description')
                            ->label('Issue Description / Notes')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\TextInput::make('estimated_cost')
                            ->label('Estimated Cost')
                            ->numeric()
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
                        Forms\Components\TextInput::make('location')
                            ->label('Location')
                            ->visible(fn ($get) => $get('device_type') === 'mobile'),
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

                        Forms\Components\TextInput::make('laptop_make')
                            ->label('Make')
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
                        Forms\Components\TextInput::make('laptop_model')
                            ->label('Model')
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
                        Forms\Components\TextInput::make('laptop_serial_number')
                            ->label('Serial Number')
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
                        Forms\Components\TextInput::make('laptop_os')
                            ->label('Operating System')
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
                        Forms\Components\TextInput::make('laptop_warranty')
                            ->label('Warranty Status')
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
                        Forms\Components\TextArea::make('laptop_issue_description')
                            ->label('Issue Description / Notes')
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
                        Forms\Components\TextInput::make('laptop_estimated_cost')
                            ->label('Estimated Cost')
                            ->numeric()
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),
                        Forms\Components\TextInput::make('laptop_location')
                            ->label('Location')
                            ->visible(fn ($get) => $get('device_type') === 'laptop'),

                        Forms\Components\TextInput::make('repair_number')
                            ->required()
                            ->unique()
                            ->default(fn () => strtoupper(Str::random(8))),
                        Forms\Components\TextInput::make('status')
                            ->required(),
                        Forms\Components\TextInput::make('quoted_price')
                            ->numeric(),
                        Forms\Components\TextInput::make('finalized_price')
                            ->numeric(),
                        Forms\Components\TextInput::make('location'),
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('repair_number'),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                Tables\Columns\TextColumn::make('device_type')->label('Device Type'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('location'),
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
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'awaiting_parts' => 'Awaiting Parts',
                                'awaiting_customer' => 'Awaiting Customer',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('finalized_price')
                            ->label('Finalized Price')
                            ->numeric(),
                    ])
                    ->action(function ($record, $data) {
                        $record->update([
                            'status' => $data['status'],
                            'finalized_price' => $data['finalized_price'],
                        ]);

                        // Send email notifications based on status
                        switch ($data['status']) {
                            case 'repaired':
                                Mail::to($record->customer->email)->send(new RepairReadyForPickup($record));
                                break;
                            case 'awaiting_parts':
                                Mail::to($record->customer->email)->send(new RepairAwaitingParts($record));
                                break;
                            case 'awaiting_customer':
                                Mail::to($record->customer->email)->send(new RepairAwaitingCustomer($record));
                                break;
                        }
                    }),

                    
            ])
            ->filters([]);
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
