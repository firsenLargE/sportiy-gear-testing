<?php

namespace App\Filament\Resources\InventoryLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class InventoryLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sn')
                    ->label('S.N.')
                    ->rowIndex(),

                TextColumn::make('productVariant.name')
                    ->label('Product Variant')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('change_type')
                    ->colors([
                        'success' => 'increase',
                        'danger' => 'decrease',
                        'primary' => 'other',
                    ])
                    ->sortable(),

                TextColumn::make('quantity_change')->sortable(),

                TextColumn::make('reference_id')->label('Reference')->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
