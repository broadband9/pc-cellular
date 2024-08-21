<?php

namespace App\Filament\Resources\RepairStatusResource\Pages;

use App\Filament\Resources\RepairStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepairStatus extends EditRecord
{
    protected static string $resource = RepairStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
