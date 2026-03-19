<?php

namespace App\Filament\Resources\Discounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class DiscountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sn')
                    ->label('S.N.')
                    ->rowIndex(),

                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('discount_type')
                    ->badge(),

                TextColumn::make('discount_value')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('productVariants.sku')
                    ->label('Variants')
                    ->badge()
                    ->limitList(2)
                    ->tooltip(fn($record) => $record->productVariants->pluck('sku')->join(', ')),

                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
            ])

            ->groups([
                Group::make('discount_type')
                    ->label('Discount Type')
                    ->collapsible(),

                Group::make('start_date')
                    ->date()
                    ->label('Start Date')
                    ->collapsible(),

                Group::make('end_date')
                    ->date()
                    ->label('End Date')
                    ->collapsible(),
            ])

            ->defaultGroup('discount_type')

            ->recordActions([
                EditAction::make(),
                ViewAction::make(),

                DeleteAction::make()
                    ->disabled(fn($record) => $record->productVariants()->exists())
                    ->tooltip('Cannot delete discount attached to product variants'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            $deletable = $records->filter(fn($record) => !$record->productVariants()->exists());
                            $skipped   = $records->filter(fn($record) => $record->productVariants()->exists());

                            $deletable->each->delete();

                            if ($skipped->isNotEmpty()) {
                                Notification::make()
                                    ->title('Some discounts were not deleted')
                                    ->body($skipped->count() . ' discounts are attached to product variants and were skipped.')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->tooltip('Discounts attached to product variants will not be deleted'),
                ]),
            ]);
    }
}
