<?php

namespace App\Filament\Resources\RFQs;

use App\Filament\Resources\RFQs\Pages\EditRFQ;
use App\Filament\Resources\RFQs\Pages\ListRFQs;
use App\Filament\Resources\RFQs\Schemas\RFQForm;
use App\Filament\Resources\RFQs\Tables\RFQTable;
use App\Models\RFQ;
use Filament\Resources\Resource;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RFQResource extends Resource
{
    protected static ?string $model = RFQ::class;

    protected static ?string $modelLabel = 'Permintaan Penawaran';
    protected static ?string $pluralModelLabel = 'Permintaan Penawaran';

    protected static string|UnitEnum|null $navigationGroup = 'Penjualan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $recordTitleAttribute = 'rfq_number';

    public static function form(Schema $schema): Schema
    {
        return RFQForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RFQTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRFQs::route('/'),
            'edit' => EditRFQ::route('/{record}/edit'),
        ];
    }
}
