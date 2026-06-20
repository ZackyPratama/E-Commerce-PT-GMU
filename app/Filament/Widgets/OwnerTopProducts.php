<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OwnerTopProducts extends TableWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): \Illuminate\Database\Query\Builder => \App\Models\OrderItem::query()
                    ->select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                    ->whereHas('order', fn(Builder $q) => $q->where('payment_status', 'paid'))
                    ->groupBy('product_name')
                    ->orderBy('total_qty', 'desc')
            )
            ->columns([
                TextColumn::make('product_name')
                    ->label('Produk')
                    ->weight('bold'),
                TextColumn::make('total_qty')
                    ->label('Terjual')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_revenue')
                    ->label('Pendapatan')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
            ])
            ->heading('Produk Terlaris')
            ->paginated(false)
            ->defaultSort('total_qty', 'desc');
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('owner');
    }
}
