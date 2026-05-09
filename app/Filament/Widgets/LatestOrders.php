<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Order::query())
            ->columns([
                TextColumn::make('order_number')
                    ->weight('bold')
                    ->url(fn($record) => OrderResource::getUrl('edit', [$record])),
                TextColumn::make('customer.name')
                    ->url(fn($record) => CustomerResource::getUrl('edit', [$record->customer])),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $status): string => match ($status) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    }),
                TextColumn::make('total')
                    ->money(currency: 'IDR', locale: 'id')
                    ->weight('bold'),
                
                TextColumn::make('created_at')
                    ->label('Ordered')
                    ->since(),
            ])
            ->heading('latest Orders')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
