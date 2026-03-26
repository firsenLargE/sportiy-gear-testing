<?php

namespace App\Filament\Resources\Addresses\Schemas;

use App\Models\District;
use App\Models\ShippingZone;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('user_id')
                    ->label('User')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, $livewire) {
                            if (method_exists($livewire, 'getRecord') && $livewire->getRecord()) {
                                return;
                            }
                            $query->whereDoesntHave('addresses');
                        }
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

                TextInput::make('name')
                    ->label('Receiver Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone_no')
                    ->label('Phone Number')
                    ->tel()
                    ->required()
                    ->maxLength(15),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->nullable(),


                Select::make('province_id')
                    ->label('Province')
                    ->relationship('province', 'name')
                    ->reactive()
                    ->required(),


                Select::make('district_id')
                    ->label('District')
                    ->options(function (callable $get) {
                        return District::query()
                            ->where('province_id', $get('province_id'))
                            ->whereHas('shippingZone', function ($query) {
                                $query->where('is_active', true);
                            })
                            ->pluck('name', 'id');
                    })
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function (callable $set, $state) {
                        // Auto assign shipping zone
                        $zone = ShippingZone::where('district_id', $state)
                            ->where('is_active', true)
                            ->first();

                        $set('shipping_zone_id', $zone?->id);
                    }),

                Hidden::make('shipping_zone_id'),


                TextInput::make('address_line1')
                    ->label('Address Line 1')
                    ->required()
                    ->maxLength(255),

                TextInput::make('address_line2')
                    ->label('Address Line 2')
                    ->nullable()
                    ->maxLength(255),

                TextInput::make('nearest_landmark')
                    ->label('Nearest Landmark')
                    ->nullable()
                    ->maxLength(255),

            ]);
    }
}
