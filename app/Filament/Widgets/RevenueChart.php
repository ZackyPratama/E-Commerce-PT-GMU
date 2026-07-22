<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Enums\PaymentStatusEnum;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected ?string $heading = 'Statistik Pendapatan';

    public ?string $filter = 'week';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $paidStatuses = [PaymentStatusEnum::PAID, 'completed'];

        $query = Order::query()->whereIn('payment_status', $paidStatuses);

        $data = match ($activeFilter) {
            'week' => Trend::query($query)
                ->between(start: now()->subWeek(), end: now())
                ->perDay()
                ->sum('total'),
            'month' => Trend::query($query)
                ->between(start: now()->subMonth(), end: now())
                ->perWeek()
                ->sum('total'),
            'year' => Trend::query($query)
                ->between(start: now()->subYear(), end: now())
                ->perMonth()
                ->sum('total'),
            default => Trend::query($query)
                ->between(start: now()->subWeek(), end: now())
                ->perDay()
                ->sum('total'),
        };

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
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
        ];
    }
}