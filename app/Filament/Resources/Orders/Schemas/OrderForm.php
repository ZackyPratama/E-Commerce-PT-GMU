<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Status Pesanan')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->label('Status Pesanan')
                            ->options([
                                'pending' => 'Menunggu Pesanan',
                                'processing' => 'Diproses',
                                'shipped' => 'Dikirim',
                                'delivered' => 'Diterima',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->native(false)
                            ->default('pending')
                            ->required(),
                        TextInput::make('tracking_number')
                            ->label('Nomor Pelacakan')
                            ->helperText('Opsional, hanya untuk status "Shipped"')
                            ->default(null),
                        Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending' => 'Menunggu Pembayaran',
                                'paid' => 'Sudah Bayar',
                                'failed' => 'Gagal',
                                'refunded' => 'Refunded',
                            ])
                            ->native(false)
                            ->required()
                            ->default('pending'),
                        Textarea::make('admin_notes')
                            ->label('Catatan Admin')
                            ->default(null)
                            ->columnSpanFull(),
                    ]),
                
            ]);
    }
}
