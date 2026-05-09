<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Nama Product')
                    ->searchable()
                    ->url(fn($record) => ProductResource::getUrl('edit', [$record->product]))
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->url(fn($record) => CustomerResource::getUrl('edit', [$record->customer]))
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('rating')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('title')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('comment')
                    ->limit(100)
                    ->wrap()
                    ->searchable(),
                IconColumn::make('is_verified_purchase')
                    ->label('Verifikasi Pembelian')
                    ->boolean(),
                IconColumn::make('is_approved')
                    ->label('Status Approval')
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
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label('Approval Status')
                    ->boolean()
                    ->trueLabel('Approved Only')
                    ->falseLabel('Pending Only')
                    ->native(false),
                TernaryFilter::make('is_verified_purchase')
                    ->label('Verified Purchase')
                    ->boolean()
                    ->trueLabel('Verified Only')
                    ->falseLabel('Unverified Only')
                    ->native(false),
            ])
            ->recordActions([
                Action::make('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn($record) => $record->update(['is_approved' => true]))
                    ->visible(fn($record) => !$record->isApproved())
                    ->requiresConfirmation(),
                Action::make('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn($record) => $record->update(['is_approved' => false]))
                    ->visible(fn($record) => $record->is_approved)
                    ->requiresConfirmation(),
                EditAction::make(),
            ])
            ->toolbarActions([
                // ini generate klo ada error aneh harusnya disini
                BulkActionGroup::make([
                    BulkAction::make('bulkApprove')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['is_approved' => true])),
                    BulkAction::make('bulkReject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn($records) => $records->each->update(['is_approved' => false])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
