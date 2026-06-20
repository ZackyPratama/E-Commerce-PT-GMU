<?php

namespace App\Filament\Widgets;

use App\Models\RFQ;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OwnerRfqOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $total = RFQ::count();
        $pending = RFQ::whereIn('status', ['submitted', 'under_review'])->count();
        $quoted = RFQ::where('status', 'quoted')->count();
        $accepted = RFQ::where('status', 'accepted')->count();

        return [
            Stat::make('Total RFQ', $total)
                ->description('Semua permintaan penawaran')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Menunggu Review', $pending)
                ->description('Perlu ditanggapi admin')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Sudah Dikutip', $quoted)
                ->description('Menunggu keputusan customer')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),

            Stat::make('Diterima', $accepted)
                ->description('RFQ berhasil dikonversi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('owner');
    }
}
