<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pelanggan')
                    ->searchable(),
                TextColumn::make('email')
                    // ->label('Email')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'b2b' ? 'warning' : 'gray')
                    ->formatStateUsing(fn(string $state): string => $state === 'b2b' ? 'B2B' : 'B2C'),
                TextColumn::make('company_name')
                    ->label('Perusahaan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('b2b_status')
                    ->label('Status B2B')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'pending' => 'Menunggu',
                        default => '-',
                    }),
                TextColumn::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Akun')
                    ->options(['b2c' => 'Perorangan (B2C)', 'b2b' => 'Perusahaan (B2B)']),
                \Filament\Tables\Filters\SelectFilter::make('b2b_status')
                    ->label('Status B2B')
                    ->options(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak']),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                Action::make('approve_b2b')
                    ->label('Setujui B2B')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Customer $record): bool => $record->type === 'b2b' && $record->b2b_status === 'pending')
                    ->action(function (Customer $record) {
                        $record->update([
                            'b2b_status' => 'approved',
                            'approved_at' => now(),
                            'is_active' => true,
                        ]);
                        Notification::make()
                            ->title('Akun B2B disetujui')
                            ->success()
                            ->send();
                    }),
                Action::make('reject_b2b')
                    ->label('Tolak B2B')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Customer $record): bool => $record->type === 'b2b' && $record->b2b_status === 'pending')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\TextInput::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (Customer $record, array $data) {
                        $record->update([
                            'b2b_status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()
                            ->title('Akun B2B ditolak')
                            ->danger()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
