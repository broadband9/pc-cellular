<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepairStatusResource\Pages;
use App\Filament\Resources\RepairStatusResource\RelationManagers;
use App\Models\RepairStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RepairStatusResource extends Resource
{
    protected static ?string $model = RepairStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignorable: fn ($record) => $record)
                    ->maxLength(255),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRepairStatuses::route('/'),
            'create' => Pages\CreateRepairStatus::route('/create'),
            'edit' => Pages\EditRepairStatus::route('/{record}/edit'),
        ];
    }
}
