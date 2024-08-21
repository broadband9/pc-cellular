<?php

namespace App\Filament\Resources\RepairStatusResource\Pages;

use App\Filament\Resources\RepairStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRepairStatuses extends ListRecords
{
    protected static string $resource = RepairStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
