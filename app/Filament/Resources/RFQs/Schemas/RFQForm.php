<?php

namespace App\Filament\Resources\RFQs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RFQForm
{
    public static function getComponents(): array
    {
        return [
            Section::make('Informasi RFQ')
                ->schema([
                    TextInput::make('rfq_number')
                        ->label('No. RFQ')
                        ->disabled(),
                    TextInput::make('customer.name')
                        ->label('Pelanggan')
                        ->disabled(),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'submitted' => 'Diajukan',
                            'under_review' => 'Ditinjau',
                            'quoted' => 'Penawaran Dikirim',
                            'accepted' => 'Diterima',
                            'rejected' => 'Ditolak',
                            'expired' => 'Kadaluarsa',
                            'converted' => 'Dikonversi ke Pesanan',
                        ])
                        ->native(false),
                    DatePicker::make('valid_until')
                        ->label('Berlaku Sampai')
                        ->native(false),
                ])->columns(3),
            Section::make('Catatan')
                ->schema([
                    Textarea::make('customer_notes')
                        ->label('Catatan Pelanggan')
                        ->disabled(),
                    Textarea::make('admin_notes')
                        ->label('Catatan Admin'),
                ])->columns(2),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(static::getComponents());
    }
}
