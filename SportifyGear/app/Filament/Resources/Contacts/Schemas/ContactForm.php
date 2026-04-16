<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->disabled(),

                TextInput::make('email')
                    ->label('Email')
                    ->disabled(),

                Textarea::make('message')
                    ->label('Message')
                    ->rows(5)
                    ->disabled(),
            ]);
    }
}
