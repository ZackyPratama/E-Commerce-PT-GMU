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
                            ->required(),
                        TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->readOnly()
                            ->visibleOn('edit'),
                        Textarea::make('description')
                            ->rows(3)
                            ->default(null)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->disk('public')
                            ->directory('categories')
                            ->imageEditor()
                            ->preserveFilenames()
                            ->downloadable()
                            ->image(),
                    ]),

                section::make('Detail tampilan')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->required(),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                section::make('Konfigurasi SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->default(null),
                        Textarea::make('meta_description')
                            ->default(null)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
