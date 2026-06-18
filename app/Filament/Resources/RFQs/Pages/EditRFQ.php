<?php

namespace App\Filament\Resources\RFQs\Pages;

use App\Filament\Resources\RFQs\RFQResource;
use App\Filament\Resources\RFQs\Schemas\RFQForm;
use App\Models\RFQItem;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class EditRFQ extends EditRecord
{
    protected static string $resource = RFQResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...RFQForm::getComponents(),
                Section::make('Item Permintaan')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                TextInput::make('product_name')
                                    ->label('Produk')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                        if ($record) {
                                            $name = $record->product?->name ?? 'Produk Tidak Tersedia';
                                            if ($record->variant) {
                                                $name .= ' - ' . $record->variant?->name;
                                            }
                                            $component->state($name);
                                        }
                                    }),
                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->disabled()
                                    ->numeric()
                                    ->dehydrated(false),
                                TextInput::make('customer_requested_price')
                                    ->label('Harga Diminta')
                                    ->disabled()
                                    ->prefix('Rp')
                                    ->dehydrated(false),
                                TextInput::make('quoted_price')
                                    ->label('Harga Ditawarkan')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->prefix('Rp')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $set, $get, $livewire, $record) {
                                        if ($record && $state !== null) {
                                            $qty = $record->quantity ?? 1;
                                            $subtotal = (float) $state * (int) $qty;
                                            $record->update([
                                                'quoted_price' => $state,
                                                'subtotal' => $subtotal,
                                            ]);
                                            $rfq = $record->rfq;
                                            $items = $rfq->items;
                                            $subtotalSum = $items->sum('subtotal');
                                            $rfq->update([
                                                'subtotal' => $subtotalSum,
                                                'total' => $subtotalSum - ($rfq->discount_amount ?? 0) + ($rfq->tax_amount ?? 0),
                                            ]);
                                        }
                                    }),
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->disabled()
                                    ->prefix('Rp')
                                    ->dehydrated(false),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->deletable(false)
                            ->addable(false)
                            ->reorderable(false),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        if ($record->status === 'submitted') {
            $record->update(['status' => 'under_review']);
        }
    }
}
