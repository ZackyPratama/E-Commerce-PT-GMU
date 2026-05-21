<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Customers')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Pelanggan')
                            ->required(),
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->required(),
                        DateTimePicker::make('email_verified_at')
                            ->label('Tanggal Verifikasi Email')
                            ->native(false),
                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->default(null),
                        DatePicker::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->native(false)
                            ->native()
                            ->displayFormat('M d, Y'),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(['male' => 'Pria', 'female' => 'Wanita'])
                            ->default(null)
                            ->native(false),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Informasi Password')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $operation) => $operation === 'create')
                            ->revealable()
                            ->required(),
                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn(string $operation) => $operation === 'create'),

                    ]),

            ]);
    }
}
