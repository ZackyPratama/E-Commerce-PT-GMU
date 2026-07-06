<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\OwnerRfqOverview;
use App\Filament\Widgets\OwnerRevenueChart;
use App\Filament\Widgets\OwnerStatsOverview;
use App\Filament\Widgets\OwnerTopProducts;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class OwnerDashboard extends Dashboard
{
    protected static string $routePath = '/';

    protected static bool $isDiscovered = false;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = -2;

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-chart-bar';
    }

    public function getWidgets(): array
    {
        return [
            OwnerStatsOverview::class,
            OwnerTopProducts::class,
            OwnerRfqOverview::class,
            OwnerRevenueChart::class,
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('owner');
    }
}
