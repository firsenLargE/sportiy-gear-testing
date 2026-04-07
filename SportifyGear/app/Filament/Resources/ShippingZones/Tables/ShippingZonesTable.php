<?php

namespace App\Filament\Resources\ShippingZones\Tables;

use Filament\Actions\BulkActionGroup;
// use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ShippingZonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('district.name')
                    ->label('District')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('district.province.name')
                    ->label('Province')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('shipping_fee')
                    ->label('Shipping Fee')
                    ->money('NPR') // cleaner than manual Rs
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->since() // shows "2 hours ago"
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            // ✅ GROUPING HERE
            ->groups([
                Group::make('district.province.name')
                    ->label('Province')
                    ->collapsible(),
            ])

            ->defaultGroup('district.province.name')

            ->filters([
                //
            ])

            ->recordActions([
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
