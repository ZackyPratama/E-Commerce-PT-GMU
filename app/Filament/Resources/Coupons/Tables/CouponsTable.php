<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Coupon')
                    ->sortable()
                    ->copyable()
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'fixed' => 'success',
                        'percentage' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('value')
                    ->label('Discount')
                    ->formatStateUsing(fn($record) => $record->type === 'percentage' ? $record->value . '%' : 'Rp ' . number_format($record->value, 2))
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('minimum_order_value')
                    ->label('Min. Order')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('usage_limit')
                    ->label('Limit Pemakaian')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('usage_count')
                    ->label('Dipakai')
                    ->counts('usages')
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Mulai Aktif')
                    ->placeholder('Active Now')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Berakhir')
                    ->dateTime()
                    ->sortable()
                    ->color(fn($state) => $state->isPast() ? 'danger' : 'gray'),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'fixed' => 'Fixed',
                        'percentage' => 'Percentage'
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
