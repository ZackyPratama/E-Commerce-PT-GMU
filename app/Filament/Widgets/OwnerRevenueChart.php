<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OwnerRevenueChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Grafik Pendapatan';

    public ?string $filter = 'week';

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $data = Trend::model(Order::class)
            ->where('payment_status', 'paid')
            ->between(
                start: match ($activeFilter) {
                    'week' => now()->subWeek(),
                    'month' => now()->subMonth(),
                    'year' => now()->subYear(),
                },
                end: now()
            )
            ->perDay()
            ->sum('total');

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => '7 Hari',
            'month' => '30 Hari',
            'year' => 'Tahun Ini',
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('owner');
    }
}
