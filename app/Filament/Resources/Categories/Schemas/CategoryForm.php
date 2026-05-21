<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required(),
                        TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->readOnly()
                            ->visibleOn('edit'),
                        Textarea::make('description')
                            ->label('Deskripsi Kategori')
                            ->rows(3)
                            ->default(null)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Gambar')
                            ->disk('public')
                            ->directory('categories')
                            ->imageEditor()
                            ->preserveFilenames()
                            ->downloadable()
                            ->image(),
                    ]),

                Section::make('Detail tampilan')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Tampilkan di Toko')
                            ->required(),
                        TextInput::make('sort_order')
                            ->label('Urutan Tampilan')  
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Konfigurasi SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Judul Meta')
                            ->default(null),
                        Textarea::make('meta_description')
                            ->label('Deskripsi Meta')
                            ->default(null)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
