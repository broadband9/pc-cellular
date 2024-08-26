<?php

namespace App\Filament\Resources\MakeResource\Pages;

use App\Filament\Resources\MakeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMake extends CreateRecord
{
    protected static string $resource = MakeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
