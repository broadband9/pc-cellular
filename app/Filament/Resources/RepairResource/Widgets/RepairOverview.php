<?php

namespace App\Filament\Resources\RepairResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Repair;
use App\Models\RepairStatus; 

class RepairOverview extends BaseWidget
{
    protected function getCards(): array
    {
        // Get all statuses from the RepairStatus model
        $statuses = RepairStatus::all();

        // Initialize an array to hold the cards
        $cards = [
            // Add the total repairs card
            Card::make('Total Repairs', Repair::count())
                ->description('All time repairs')
                ->descriptionIcon('heroicon-o-cog-6-tooth'),
        ];

        // Add a card for each status
        foreach ($statuses as $status) {
            $cards[] = Card::make($status->name, Repair::where('status_id', $status->id)->count())
                ->description("Repairs with status: {$status->name}")
                ->descriptionIcon('heroicon-o-check-circle');
        }

        return $cards;
    }
}
