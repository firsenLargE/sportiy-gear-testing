<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->email})")
                    ->searchable(['name', 'email', 'phone_no'])
                    ->preload()
                    ->required(),

                Select::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->searchable(['name', 'slug'])
                    ->preload()
                    ->required(),

                Select::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '⭐',
                        2 => '⭐⭐',
                        3 => '⭐⭐⭐',
                        4 => '⭐⭐⭐⭐',
                        5 => '⭐⭐⭐⭐⭐',
                    ])
                    ->required(),

                Textarea::make('comment')
                    ->label('Comment')
                    ->rows(4)
                    ->columnSpanFull(),

            ]);
    }
}
