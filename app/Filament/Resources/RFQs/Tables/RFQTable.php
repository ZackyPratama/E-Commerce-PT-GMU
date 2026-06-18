<?php

namespace App\Filament\Resources\RFQs\Tables;

use App\Mail\RFQQuoted;
use App\Mail\RFQRejected;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RFQ;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RFQTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rfq_number')
                    ->label('No. RFQ')
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable(),
                TextColumn::make('customer.company_name')
                    ->label('Perusahaan')
                    ->searchable(),
                TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->numeric(),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'info',
                        'under_review' => 'warning',
                        'quoted' => 'success',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'expired' => 'gray',
                        'converted' => 'purple',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'under_review' => 'Ditinjau',
                        'quoted' => 'Penawaran Dikirim',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak',
                        'expired' => 'Kadaluarsa',
                        'converted' => 'Dikonversi',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'submitted' => 'Diajukan',
                        'under_review' => 'Ditinjau',
                        'quoted' => 'Penawaran Dikirim',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak',
                        'converted' => 'Dikonversi',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('review')
                    ->label('Review & Beri Harga')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->visible(fn(RFQ $record): bool => in_array($record->status, ['submitted', 'under_review']))
                    ->url(fn(RFQ $record): string => \App\Filament\Resources\RFQs\RFQResource::getUrl('edit', ['record' => $record])),
                Action::make('send_quotation')
                    ->label('Kirim Penawaran')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(RFQ $record): bool => $record->status === 'under_review')
                    ->requiresConfirmation()
                    ->action(function (RFQ $record) {
                        $record->update(['status' => 'quoted']);
                        Mail::to($record->customer->email)->queue(new RFQQuoted($record));
                        Notification::make()
                            ->title('Penawaran berhasil dikirim ke pelanggan')
                            ->success()
                            ->send();
                    }),
                Action::make('accept')
                    ->label('Terima & Buat Pesanan')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn(RFQ $record): bool => $record->status === 'quoted')
                    ->requiresConfirmation()
                    ->action(function (RFQ $record) {
                        try {
                            DB::beginTransaction();

                            $customer = $record->customer;
                            $defaultAddress = $customer->addresses()->where('is_default', true)->first();

                            $subtotal = $record->subtotal;
                            $total = ($record->total ?: $subtotal);

                            $order = Order::create([
                                'customer_id' => $customer->id,
                                'rfq_id' => $record->id,
                                'subtotal' => $subtotal,
                                'discount_amount' => $record->discount_amount ?? 0,
                                'shipping_cost' => 0,
                                'tax_amount' => $record->tax_amount ?? 0,
                                'total' => $total,
                                'payment_method' => 'midtrans',
                                'payment_status' => 'pending',
                                'status' => 'pending',
                                'customer_notes' => $record->customer_notes,
                                'shipping_full_name' => $defaultAddress?->full_name ?? $customer->name,
                                'shipping_phone' => $defaultAddress?->phone ?? $customer->phone,
                                'shipping_address_line_1' => $defaultAddress?->address_line_1 ?? '',
                                'shipping_address_line_2' => $defaultAddress?->address_line_2,
                                'shipping_city' => $defaultAddress?->city ?? '',
                                'shipping_state' => $defaultAddress?->state,
                                'shipping_postal_code' => $defaultAddress?->postal_code ?? '',
                                'shipping_country' => $defaultAddress?->country ?? 'ID',
                            ]);

                            foreach ($record->items as $item) {
                                OrderItem::create([
                                    'order_id' => $order->id,
                                    'product_id' => $item->product_id,
                                    'product_variant_id' => $item->product_variant_id,
                                    'product_name' => $item->product?->name ?? 'Produk',
                                    'product_sku' => $item->variant?->sku ?? $item->product?->sku ?? '',
                                    'variant_name' => $item->variant?->name,
                                    'price' => $item->quoted_price,
                                    'quantity' => $item->quantity,
                                    'subtotal' => $item->subtotal,
                                ]);
                            }

                            $record->update(['status' => 'converted']);

                            DB::commit();

                            Notification::make()
                                ->title('RFQ diterima, pesanan berhasil dibuat')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Notification::make()
                                ->title('Gagal membuat pesanan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn(RFQ $record): bool => in_array($record->status, ['submitted', 'under_review', 'quoted']))
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\TextInput::make('rejection_reason')
                            ->label('Alasan')
                            ->required(),
                    ])
                    ->action(function (RFQ $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $record->admin_notes ? $record->admin_notes . "\n\nAlasan ditolak: " . $data['rejection_reason'] : 'Alasan ditolak: ' . $data['rejection_reason'],
                        ]);
                        Mail::to($record->customer->email)->queue(new RFQRejected($record));
                        Notification::make()
                            ->title('RFQ ditolak')
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
