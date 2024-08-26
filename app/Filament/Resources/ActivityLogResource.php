<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Activity Logs';
    protected static ?string $pluralLabel = 'Activity Logs';
    protected static ?string $slug = 'activity-logs';
    protected static ?int $navigationSort = 100;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Log Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Caused By')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Define filters here, if needed
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Activity Details')
                    ->modalContent(function ($record) {
                        $properties = $record->properties->toArray();
                        $formattedProperties = '';

                        foreach ($properties as $key => $value) {
                            $formattedProperties .= '<p><strong>' . ucfirst(str_replace('_', ' ', $key)) . ':</strong> ';
                            if (is_array($value) || is_object($value)) {
                                $formattedProperties .= '<pre>' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</pre>';
                            } else {
                                $formattedProperties .= htmlspecialchars($value);
                            }
                            $formattedProperties .= '</p>';
                        }

                        return new HtmlString($formattedProperties);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            // The 'create' page is intentionally omitted
            // 'edit' => Pages\EditActivityLog::route('/{record}/edit'), // Optional, based on your needs
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Logs';
    }
}
