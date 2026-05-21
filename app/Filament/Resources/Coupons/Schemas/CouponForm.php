<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('informasi Coupon')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Coupon')
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('code', strtoupper($state)))
                            ->required(),
                        Select::make('type')
                            ->label('Jenis Coupon')
                            ->options(['fixed' => 'Fixed', 'percentage' => 'Percentage'])
                            ->default('percentage')
                            ->live()
                            ->required(),
                        TextInput::make('value')
                            ->label('Jumlah Discount')
                            ->required()
                            ->minValue(0)
                            ->prefix(fn(callable $get) => $get('type') === 'fixed' ? 'Rp ' : null)
                            ->suffix(fn(callable $get) => $get('type') === 'percentage' ? '%' : null)
                            ->numeric(),
                        Toggle::make('is_active')
                            ->label('Tampilkan Coupon')
                            ->required(),

                    ]),
                Section::make('Ketentuan & Limit Coupon ')
                    ->schema([
                        TextInput::make('minimum_order_value')
                            ->label('Jumlah Pesanan Minimum')
                            ->prefix('Rp ')
                            ->minValue(0)
                            ->numeric()
                            ->default(null),
                        TextInput::make('maximum_discount')
                            ->label('Diskon Maksimal')
                            ->numeric()
                            ->prefix('Rp ')
                            ->minValue(0)
                            ->visible(fn(callable $get) => $get('type') === 'percentage')
                            ->default(null),
                        TextInput::make('usage_limit')
                            ->label('Limit Pemakaian')
                            ->minValue(1)
                            ->numeric()
                            ->default(null),
                        TextInput::make('usage_limit_per_customer')
                            ->label('Limit Pemakaian Per Customer')
                            ->numeric()
                            ->minValue(1)
                            ->default(null),
                    ]),

                    Section::make('Periode Aktif Coupon')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Mulai Aktif')
                            ->native(false)
                            ->helperText('Tanggal dan waktu mulai aktifnya coupon'),
                        DateTimePicker::make('expires_at')
                            ->label('Berakhir')
                            ->native(false)
                            ->helperText('Tanggal dan waktu berakhirnya coupon'),
                    ]),
                
            ]);
    }
}
