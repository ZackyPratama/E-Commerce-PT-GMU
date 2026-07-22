<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Enums\PaymentStatusEnum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OwnerTopProducts extends TableWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $paidStatuses = [PaymentStatusEnum::PAID, 'completed'];

        return $table
            ->query(
                fn(): Builder => OrderItem::query()
                    ->select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                    ->whereHas('order', fn(Builder $q) => $q->whereIn('payment_status', $paidStatuses))
                    ->groupBy('product_name')
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

    public function getTableRecordKey(Model | array $record): string
    {
        return $record->product_name;
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('owner');
    }
}
