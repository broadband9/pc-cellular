<?php

namespace App\Filament\Resources\RepairStatusChartResource\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Repair;
use Carbon\Carbon;

class RepairStatusChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string|null
     */
    protected static ?string $chartId = 'repairStatusChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Repair Status Chart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Prepare data for the last 30 days
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(30);
        
        // Fetch data grouped by date
        $data = Repair::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            });

        // Generate a complete list of dates
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }

        // Ensure all dates are present in data
        $data = collect($dates)->mapWithKeys(function ($date) use ($data) {
            return [$date => $data->get($date, 0)];
        });

        return [
            'chart' => [
                'type' => 'bar', // Changed to bar chart
                'height' => 350,
                'animations' => [ // Disable animations
                    'enabled' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Number of Repairs',
                    'data' => $data->values()->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $data->keys()->toArray(),
                'title' => [
                    'text' => 'Date',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Number of Repairs',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'tooltip' => [
                'enabled' => true,
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'legend' => [
                'show' => true,
            ],
            'colors' => ['#f59e0b'], // Customize this color
        ];
    }
}
