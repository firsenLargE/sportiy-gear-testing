<?php

namespace App\Filament\Resources\Addresses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // User Selection
                Select::make('user_id')
                    ->label('User')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query, $livewire) =>
                        $query->whereDoesntHave('address')
                            ->when(
                                $livewire->record,
                                fn($query) => $query->orWhere('id', $livewire->record->user_id)
                            )
                    )
                    ->searchable(['name', 'email'])
                    ->getOptionLabelFromRecordUsing(
                        fn($record) => "{$record->name} ({$record->email})"
                    )
                    ->preload()
                    ->required()
                    ->unique(
                        table: 'addresses',
                        column: 'user_id',
                        ignoreRecord: true
                    ),

                // Receiver Name
                TextInput::make('name')
                    ->label('Receiver Name')
                    ->required()
                    ->maxLength(255),

                // Phone Number
                TextInput::make('phone_no')
                    ->label('Phone Number')
                    ->tel()
                    ->required()
                    ->maxLength(15),

                // Email
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->nullable(),

                // Province
                TextInput::make('province')
                    ->required()
                    ->maxLength(100),

                // District
                TextInput::make('district')
                    ->required()
                    ->maxLength(100),

                // Address Line 1
                TextInput::make('address_line1')
                    ->label('Address Line 1')
                    ->required()
                    ->maxLength(255),

                // Address Line 2
                TextInput::make('address_line2')
                    ->label('Address Line 2')
                    ->nullable()
                    ->maxLength(255),

                // Landmark
                TextInput::make('nearest_landmark')
                    ->label('Nearest Landmark')
                    ->nullable()
                    ->maxLength(255),

            ]);
    }
}
