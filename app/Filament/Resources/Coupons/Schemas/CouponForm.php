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
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('code', strtoupper($state)))
                            ->required(),
                        Select::make('type')
                            ->options(['fixed' => 'Fixed', 'percentage' => 'Percentage'])
                            ->default('percentage')
                            ->live()
                            ->required(),
                        TextInput::make('value')
                            ->required()
                            ->minValue(0)
                            ->prefix(fn(callable $get) => $get('type') === 'fixed' ? 'Rp ' : null)
                            ->suffix(fn(callable $get) => $get('type') === 'percentage' ? '%' : null)
                            ->numeric(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->required(),

                    ]),
                Section::make('Ketentuan & Limit Coupon ')
                    ->schema([
                        TextInput::make('minimun_order_value')
                            ->prefix('Rp ')
                            ->minValue(0)
                            ->numeric()
                            ->default(null),
                        TextInput::make('maximun_discount')
                            ->numeric()
                            ->prefix('Rp ')
                            ->minValue(0)
                            ->visible(fn(callable $get) => $get('type') === 'percentage')
                            ->default(null),
                        TextInput::make('usage_limit')
                            ->minValue(1)
                            ->numeric()
                            ->default(null),
                        TextInput::make('usage_limit_per_customer')
                            ->numeric()
                            ->minValue(1)
                            ->default(null),
                    ]),

                    Section::make('Periode Aktif Coupon')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->native(false)
                            ->helperText('Tanggal dan waktu mulai aktifnya coupon'),
                        DateTimePicker::make('expires_at')
                            ->native(false)
                            ->helperText('Tanggal dan waktu berakhirnya coupon'),
                    ]),
                
            ]);
    }
}
