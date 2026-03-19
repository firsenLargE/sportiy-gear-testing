<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // Parent Category
                Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->placeholder('None (Top Level Category)')
                    ->helperText('Leave empty to create a main category')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('New Parent Category Name')
                            ->required()
                            ->unique(Category::class, 'name')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Category::class, 'slug')
                            ->helperText('Automatically generated from name'),
                    ]),


                // Category Name
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->unique(Category::class, 'name', ignoreRecord: true)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),


                // Slug
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(Category::class, 'slug', ignoreRecord: true)
                    ->helperText('Automatically generated from name'),

            ]);
    }
}
