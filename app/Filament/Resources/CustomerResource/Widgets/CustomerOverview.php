<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer; 
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class CustomerOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Customers', Customer::count())
                ->description('All time customers')
                ->descriptionIcon('heroicon-o-users'),
        ];
    }
}

