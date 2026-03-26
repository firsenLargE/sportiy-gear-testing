<?php

namespace App\Filament\Resources\Addresses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class AddressesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([


                TextColumn::make('sn')
                    ->label('S.N.')
                    ->rowIndex(),


                TextColumn::make('user.name')
                    ->label('User')
                    ->icon('heroicon-o-user')
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('name')
                    ->label('Receiver')
                    ->icon('heroicon-o-identification')
                    ->searchable(),

                TextColumn::make('phone_no')
                    ->label('Phone')
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->searchable(),


                TextColumn::make('province.name')
                    ->label('Province')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('district.name')
                    ->label('District')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('full_address')
                    ->label('Address')
                    ->getStateUsing(
                        fn($record) =>
                        collect([
                            $record->address_line1,
                            $record->address_line2,
                            $record->nearest_landmark,
                        ])->filter()->implode(', ')
                    )
                    ->wrap()
                    ->limit(40),

                IconColumn::make('shippingZone.is_active')
                    ->label('Shipping')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),


                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])


            ->groups([
                Group::make('province.name')
                    ->label('Province'),

                Group::make('district.name')
                    ->label('District'),

                Group::make('created_at')
                    ->date()
                    ->label('Created Date'),
            ])

            ->defaultGroup('province.name')


            ->filters([
                //
            ])


            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
