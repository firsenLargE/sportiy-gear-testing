<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;

class CouponUsagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sn')
                    ->label('S.N.')
                    ->rowIndex(),

                TextColumn::make('coupon.code')
                    ->label('Coupon Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('used_at')
                    ->label('Used At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->toggleable(),
            ])

            ->groups([
                Group::make('coupon.code')
                    ->label('Coupon'),

                Group::make('user.name')
                    ->label('User'),

                Group::make('used_at')
                    ->date()
                    ->label('Usage Date'),
            ])

            ->defaultGroup('coupon.code')

            ->headerActions([])
            ->recordActions([])
            ->defaultSort('used_at', 'desc');
    }
}
