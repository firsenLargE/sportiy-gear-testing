<?php

namespace App\Filament\Resources\Attributes\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class AttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Attribute Information')
                    ->description('Create attribute and manage its values')
                    ->schema([

                        TextInput::make('name')
                            ->label('Attribute Name')
                            ->required()
                            ->maxLength(255)
                            ->unique('attributes', 'name', ignoreRecord: true),

                        Repeater::make('values')
                            ->relationship('values')
                            ->label('Attribute Values')
                            ->schema([
                                TextInput::make('value')
                                    ->label('Value')
                                    ->required()
                                    ->maxLength(255)
                                    ->rule(function ($get, $record) {
                                        $attributeId = $get('../../id');
                                        return Rule::unique('attribute_values', 'value')
                                            ->where('attribute_id', $attributeId)
                                            ->ignore($record?->id);
                                    }),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Add Value')
                            ->cloneable()
                            ->collapsible()
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }
}
