<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;
    protected ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $todayRevenue = Order::where('payment_status', 'paid')
        ->whereDate('created_at', 'today')->sum('total');

        $totalOrder = Order::count();
        $pendingOrder = Order::where('status', 'pending')->count();

        $totalCustomer = Customer::count();
        $newCustomerThisMonth = Customer::whereMonth('created_at',now()->month)
        ->whereYear('created_at',now()->year)
        ->count();

        // widgets jika stok menipis
        $lowStockProduct = Product::where('stock_status', 'low_stock')->count();
        // $lowStockProduct = Product::lowStock()->count();
        

        return [
            Stat::make('Total Revenue', number_format($totalRevenue, 2))
            ->description('Hari ini Rp. ' . number_format($todayRevenue, 2))
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),

            Stat::make('Total Order', $totalOrder)
            ->description($pendingOrder . ' pending')
            ->descriptionIcon('heroicon-m-shopping-cart')
            ->color('warning')
            ->url(route('filament.admin.resources.orders.index')),

            Stat::make('Total Customer', $totalCustomer)
            ->description($newCustomerThisMonth . ' new this month')
            ->descriptionIcon('heroicon-m-user-group')
            ->color('info')
            ->url(route('filament.admin.resources.customers.index')),

            Stat::make('Low Stock Alert', $lowStockProduct)
            ->description('Product Menipis')
            ->descriptionIcon('heroicon-m-exclamation-triangle')
            ->color('danger')
            ->url(route('filament.admin.resources.products.index')),
        ];
    }
}
