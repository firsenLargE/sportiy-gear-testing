<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn($context) => $context === 'create')
                    ->minLength(8)
                    ->rule(Password::default())
                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => filled($state)),

                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->required(fn($context) => $context === 'create')
                    ->same('password')
                    ->label('Confirm Password'),

            ]);
    }
}
