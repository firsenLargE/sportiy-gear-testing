<?php

namespace App\Filament\Resources\InventoryLogs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class InventoryLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_variant_id')
                    ->label('Product Variant')
                    ->relationship('productVariant', 'id')
                    ->required()
                    ->searchable()
                    ->getOptionLabelUsing(function ($value, $record) {
                        return $record->product->name . ' - ' . $record->name;
                    }),

                TextInput::make('change_type')
                    ->required()
                    ->maxLength(255),

                TextInput::make('quantity_change')
                    ->required()
                    ->numeric(),

                TextInput::make('reference_id')
                    ->label('Reference ID')
                    ->maxLength(255),
            ]);
    }
}
