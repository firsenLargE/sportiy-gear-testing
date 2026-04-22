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
                        modifyQueryUsing: function (Builder $query) {
                            // Allow any user (admin can assign address to anyone)
                            $query->orderBy('name');
                        }
                    )
                    ->searchable(['name', 'email'])
                    ->getOptionLabelFromRecordUsing(
                        fn($record) => "{$record->name} ({$record->email})"
                    )
                    ->preload()
                    ->required(),

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
                        $provinceId = $get('province_id');
                        if (!$provinceId) {
                            return [];
                        }
                        return District::query()
                            ->where('province_id', $provinceId)
                            ->whereHas('shippingZones', function ($query) {
                                $query->where('is_active', true);
                            })
                            ->pluck('name', 'id');
                    })
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                        // Auto assign shipping zone using both province and district
                        $provinceId = $get('province_id');
                        if ($provinceId && $state) {
                            $zone = ShippingZone::where('province_id', $provinceId)
                                ->where('district_id', $state)
                                ->where('is_active', true)
                                ->first();
                            $set('shipping_zone_id', $zone?->id);
                        } else {
                            $set('shipping_zone_id', null);
                        }
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
