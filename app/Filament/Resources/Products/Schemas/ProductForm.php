<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;


class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Detail Product')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Info Produk')
                            ->icon(Heroicon::InformationCircle)
                            ->schema([
                                Section::make('Detail Produk')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Produk')
                                            ->required(),
                                        TextInput::make('slug')
                                            ->unique(ignoreRecord: true)
                                            ->visible(fn(string $operation) => $operation === 'edit')
                                            ->required(),
                                        Select::make('category_id')
                                            ->label('Kategori')
                                            ->relationship('category', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->required()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required(),
                                                TextInput::make('slug')
                                                    ->unique(ignoreRecord: true)
                                                    ->readOnly()
                                                    ->visibleOn('edit'),
                                            ]),

                                        Select::make('brand_id')
                                            ->relationship('brand', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->default(null)
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required(),
                                                TextInput::make('slug')
                                                    ->visibleOn('edit')
                                                    ->readOnly()
                                                    ->unique(ignoreRecord: true)
                                                    ->required(),
                                            ]),
                                    ])
                                    ->columns(2),
                                Section::make('Deskripsi Produk')
                                    ->schema([
                                        Textarea::make('short_description')
                                            ->label('Deskripsi Singkat')
                                            ->default(null)
                                            ->columnSpanFull(),
                                        RichEditor::make('description')
                                            ->label('Deskripsi Lengkap')
                                            ->default(null)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Harga & Stok')
                            ->icon(Heroicon::CurrencyDollar)
                            ->schema([
                                Section::make('Harga Produk')
                                    ->schema([
                                        TextInput::make('sku')
                                            ->label('SKU')
                                            ->unique(ignoreRecord: true)
                                            ->default(fn() => 'SKU-' . strtoupper(Str::random(8)))
                                            ->helperText('Stock Keeping Unit, kode unik untuk mengidentifikasi produk')
                                            ->required(),
                                        TextInput::make('price')
                                            ->label('Harga Jual')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->helperText('Harga jual produk ditampilkan (sudah termasuk diskon)')
                                            ->prefix('Rp'),
                                        TextInput::make('compare_price')
                                            ->label('Harga Banding')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->helperText('Harga produk sebelum diskon')
                                            ->prefix('Rp'),
                                        TextInput::make('cost_price')
                                            ->label('Harga Beli')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->helperText('Harga beli produk dari supplier (untuk menghitung margin keuntungan)')
                                            ->prefix('Rp'),
                                    ])->columns(2),
                                Section::make('Stok Produk')
                                    ->schema([
                                        Toggle::make('manage_stock')
                                            ->label('Kelola Stok')
                                            ->default(true)
                                            ->helperText('Aktifkan untuk mengelola stok produk')
                                            ->live(),
                                        TextInput::make('stock_quantity')
                                            ->label('Jumlah Stok')
                                            ->required(fn(callable $get) => $get('manage_stock'))
                                            ->disabled(fn(callable $get) => !$get('manage_stock'))
                                            ->numeric()
                                            ->default(0),
                                        TextInput::make('low_stock_threshold')
                                            ->label('Batas Stok Rendah')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->helperText('memberikan peringatan ketika stok mencapai jumlah ini'),
                                        ToggleButtons::make('stock_status')
                                            ->label('Status Stok')
                                            ->options([
                                                'in_stock' => 'Tersedia',
                                                'out_of_stock' => 'Habis',
                                                'on_backorder' => 'Akan restock',
                                            ])
                                            ->grouped()
                                            ->default('in_stock')
                                            ->required(),
                                        TextInput::make('weight')
                                            ->label('Berat (kg)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->helperText('Berat produk (kg) untuk perhitungan ongkos kirim')
                                            ->default(null),
                                    ]),
                                // ->columns(2),
                            ]),
                        Tab::make('Gambar')
                            ->icon(Heroicon::Photo)
                            ->schema([
                                Section::make('Gambar Produk')
                                    ->description('Unggah gambar produk untuk ditampilkan di halaman produk')
                                    ->schema([
                                        FileUpload::make('images')
                                            ->label('Gambar Produk')
                                            ->multiple()
                                            ->image()
                                            ->directory('products')
                                            ->imageEditor()
                                            ->maxSize(2048) // 2MB
                                            ->reorderable()
                                            ->columnSpanFull()
                                            ->helperText('Upload gambar produk. Anda dapat mendrag dan drop gambar di sini.')
                                            ->saveRelationshipsUsing(function ($component, $state, $record) {
                                                // Hapus gambar yang sudah ada jika ada
                                                $record->images()->delete();

                                                if (is_array($state)) {
                                                    foreach ($state as $index => $imagePath) {
                                                        $record->images()->create([
                                                            'image_path' => $imagePath,
                                                            'is_primary' => $index === 0, // Set gambar pertama sebagai primary
                                                            'sort_order' => $index,
                                                        ]);
                                                    }
                                                }
                                            })
                                            ->dehydrated(false)
                                    ]),
                            ]),

                        Tab::make('Jenis & Variasi')
                            ->icon(Heroicon::Squares2x2)
                            ->schema([
                                Section::make('Variasi Produk')
                                    ->schema([
                                        Toggle::make('has_variants')
                                            ->live()
                                            ->label('Tampilkan Variasi Produk')
                                            ->helperText('Tampilkan variasi produk jika memiliki variasi seperti jenis atau tipe')
                                            ->required(),
                                        Section::make('Daftar Variasi')
                                            ->description('Kelola variasi produk seperti jenis, ukuran, atau warna')
                                            ->schema([
                                                Repeater::make('variants')
                                                    ->relationship('variants')
                                                    ->schema([
                                                        TextInput::make('name')
                                                            ->required()
                                                            ->label('Nama Variant')
                                                            ->placeholder('Contoh: Warna Merah, Ukuran L'),
                                                        KeyValue::make('options'),
                                                        TextInput::make('sku')
                                                            ->label('SKU')
                                                            ->unique(ignoreRecord: true)
                                                            // ->helperText('Stock Keeping Unit, kode unik untuk mengidentifikasi produk')
                                                            ->default(fn() => 'VAR-' . strtoupper(Str::random(8)))
                                                            ->required()
                                                            ->columnSpan(2),
                                                        TextInput::make('price')
                                                            ->label('Harga Jual')
                                                            ->required()
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->step(0.01)
                                                            // ->helperText('Harga jual produk ditampilkan (sudah termasuk diskon)')
                                                            ->prefix('Rp'),
                                                        TextInput::make('compare_price')
                                                            ->label('Harga Banding')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->step(0.01)
                                                            // ->helperText('Harga produk sebelum diskon')
                                                            ->prefix('Rp'),
                                                        TextInput::make('stock_quantity')
                                                            ->label('Jumlah Stok')
                                                            ->required()
                                                            ->minValue(0)
                                                            ->numeric()
                                                            ->default(0),
                                                        Select::make('stock_status')
                                                            ->label('Status Stok')
                                                            ->options([
                                                                'in_stock' => 'Tersedia',
                                                                'out_of_stock' => 'Habis',
                                                                'on_backorder' => 'Akan restock',
                                                            ])
                                                            ->default('in_stock')
                                                            ->required()
                                                            ->native(false),
                                                        Toggle::make('is_active')
                                                            ->label('Aktifkan Variant')
                                                            ->default(true),

                                                    ])
                                                    ->columns(2)
                                                    ->defaultItems(0)
                                                    ->collapsible()
                                                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                                                    ->addActionLabel('Tambah Variant'),
                                            ])
                                            ->visible(fn(callable $get) => $get('has_variants'))
                                            ->columnSpanFull()
                                    ])
                            ]),

                        Tab::make('Pengaturan')
                            ->icon(Heroicon::Cog6Tooth)
                            ->schema([
                                Section::make(('Status Product'))
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Aktifkan Produk')
                                            ->helperText('Menampilkan produk di toko online')
                                            ->required(),
                                        Toggle::make('is_featured')
                                            ->label('Tampilkan di Beranda')
                                            ->helperText('Menampilkan produk di halaman beranda')
                                            ->required(),
                                    ])
                                    ->columns(2),
                                Section::make('Statistik Product')
                                    ->schema([
                                        Placeholder::make('views_count')
                                            ->label('Jumlah Dilihat')
                                            ->content(fn($record) => $record?->views_count ?? 0),
                                        Placeholder::make('created_at')
                                            ->label('Tanggal Dibuat')
                                            ->content(fn($record) => $record?->created_at?->diffForHumans() ?? '-'),
                                    ]),
                            ]),

                        Tab::make('SEO')
                            ->icon(Heroicon::MagnifyingGlass)
                            ->schema([
                                Section::make('Search Engine Optimization (SEO)')
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label('Meta Title')
                                            ->default(null),
                                        Textarea::make('meta_description')
                                            ->label('Meta Description')
                                            ->default(null)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Section::make('Statistik Product')
                                    ->schema([
                                        Placeholder::make('views_count')
                                            ->label('Jumlah Dilihat')
                                            ->content(fn($record) => $record?->views_count ?? 0),
                                        Placeholder::make('created_at')
                                            ->label('Tanggal Dibuat')
                                            ->content(fn($record) => $record?->created_at?->diffForHumans() ?? '-'),
                                    ])

                            ])
                    ]),
            ]);
    }
}
