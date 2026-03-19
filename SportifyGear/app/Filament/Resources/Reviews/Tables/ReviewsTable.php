<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
// use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()

            ->columns([
                TextColumn::make('sn')
                    ->label('S.N.')
                    ->rowIndex(),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->user?->name . ' (' . $record->user?->email . ')'
                    )
                    ->searchable(['name', 'email'])
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->weight('bold'),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-cube')
                    ->badge()
                    ->color('info'),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        5 => 'success',
                        4 => 'info',
                        3 => 'warning',
                        2, 1 => 'danger',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state))
                    ->sortable(),

                TextColumn::make('comment')
                    ->label('Review')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->comment)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Posted')
                    ->since()
                    ->icon('heroicon-o-clock')
                    ->sortable(),
            ])

            ->groups([

                Group::make('product.name')
                    ->label('Product')
                    ->collapsible(),

                Group::make('user_id')
                    ->label('Customer')
                    ->getTitleFromRecordUsing(
                        fn($record) =>
                        $record->user?->name . ' (' . $record->user?->email . ')'
                    )
                    ->collapsible(),

            ])

            ->defaultGroup('product.name')

            ->filters([
                //
            ])

            ->recordActions([
                ViewAction::make(),
                // EditAction::make()
                //     ->icon('heroicon-o-pencil-square'),
                DeleteAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
