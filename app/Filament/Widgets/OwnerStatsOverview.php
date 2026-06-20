<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OwnerStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $todayRevenue = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        $weekRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total');

        $monthRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $totalOrders = Order::where('payment_status', 'paid')->count();

        return [
            Stat::make('Penjualan Hari Ini', 'Rp' . number_format($todayRevenue, 0, ',', '.'))
                ->description(number_format($totalOrders, 0, ',', '.') . ' pesanan dibayar')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Penjualan Minggu Ini', 'Rp' . number_format($weekRevenue, 0, ',', '.'))
                ->description(now()->startOfWeek()->format('d M') . ' - ' . now()->endOfWeek()->format('d M Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Penjualan Bulan Ini', 'Rp' . number_format($monthRevenue, 0, ',', '.'))
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('owner');
    }
}
