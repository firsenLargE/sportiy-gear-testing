<?php

namespace App\Filament\Resources\ShippingZones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Province;
use App\Models\District;

class ShippingZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('province_id')
                ->label('Province')
                ->options(Province::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->live()
                ->placeholder('Select a province')
                ->prefixIcon('heroicon-o-map')
                ->afterStateUpdated(fn($set) => $set('district_id', null))
                ->required()
                ->helperText('Select province to load districts'),

            Select::make('district_id')
                ->label('District')
                ->options(function ($get) {
                    $provinceId = $get('province_id');

                    if (!$provinceId) {
                        return [];
                    }

                    return District::where('province_id', $provinceId)
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->preload()
                ->placeholder('Select a district')
                ->prefixIcon('heroicon-o-map-pin')
                ->required()
                ->helperText('Filtered based on selected province'),


            TextInput::make('shipping_fee')
                ->label('Shipping Fee')
                ->numeric()
                ->prefix('Rs')
                ->placeholder('0.00')
                ->default(0)
                ->required()
                ->prefixIcon('heroicon-o-banknotes')
                ->helperText('Enter delivery charge'),


            Toggle::make('is_active')
                ->label('Active Status')
                ->default(true)
                ->inline(false)
                ->onIcon('heroicon-o-check-circle')
                ->offIcon('heroicon-o-x-circle')
                ->helperText('Enable or disable this shipping zone'),

        ]);
    }
}
