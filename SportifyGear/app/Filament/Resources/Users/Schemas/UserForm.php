<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('User Details')
                    ->description('Manage user information and security.')
                    ->columnSpanFull()
                    ->components([

                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->placeholder('Enter full name')
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignorable: fn($record) => $record
                            )
                            ->placeholder('Enter email')
                            ->columnSpanFull(),

                        TextInput::make('phone_no')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20)
                            ->regex('/^\+?\d{7,20}$/')
                            ->nullable()
                            ->unique(
                                table: 'users',
                                column: 'phone_no',
                                ignorable: fn($record) => $record
                            )
                            ->placeholder('Optional')
                            ->columnSpanFull(),

                        Select::make('gender')
                            ->label('Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])
                            ->placeholder('Select gender')
                            ->native(false)
                            ->columnSpanFull(),

                        Select::make('account_status')
                            ->label('Account Status')
                            ->options([
                                'active' => 'Active',
                                'pending' => 'Pending',
                                'suspended' => 'Suspended',
                                'banned' => 'Banned',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false)
                            ->columnSpanFull(),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->minLength(6)
                            ->required(fn($record) => $record === null)
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
