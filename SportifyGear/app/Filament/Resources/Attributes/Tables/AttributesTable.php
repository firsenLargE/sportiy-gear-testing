<?php

namespace App\Filament\Resources\Attributes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class AttributesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // Serial Number
                TextColumn::make('sn')
                    ->label('S.N.')
                    ->rowIndex(),

                // Attribute Name
                TextColumn::make('name')
                    ->label('Attribute')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-tag')
                    ->color('primary')
                    ->description(fn($record) => $record->values->count() . ' values'),

                // Attribute Values
                TextColumn::make('values.value')
                    ->label('Values')
                    ->badge()
                    ->color('success')
                    ->separator(',')
                    ->limitList(4)
                    ->expandableLimitedList(),

                // Used in Variants
                TextColumn::make('values.variants')
                    ->label('Used In Variants')
                    ->getStateUsing(function ($record) {
                        $variants = $record->values
                            ->flatMap(fn($v) => $v->variants)
                            ->pluck('name')
                            ->unique();

                        return $variants->join(', ') ?: 'Not Used';
                    })
                    ->badge()
                    ->color(fn($state) => $state === 'Not Used' ? 'gray' : 'warning')
                    ->wrap(),

                // Created Date
                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->groups([
                Group::make('name')
                    ->label('Attribute Name')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => $record->name),

                Group::make('created_at')
                    ->label('Created Date')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => $record->created_at?->format('Y-m-d') ?? 'N/A'),
            ])
            ->defaultGroup('name')

            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->icon('heroicon-o-pencil-square'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->disabled(fn($record) => $record->values()->whereHas('variants')->exists())
                    ->tooltip('Cannot delete attribute attached to product variants'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->action(function ($records) {

                            $deletable = $records->filter(
                                fn($record) => !$record->values()->whereHas('variants')->exists()
                            );

                            $skipped = $records->filter(
                                fn($record) => $record->values()->whereHas('variants')->exists()
                            );

                            $deletable->each->delete();

                            if ($skipped->isNotEmpty()) {
                                Notification::make()
                                    ->title('Some attributes were not deleted')
                                    ->body($skipped->count() . ' attributes are attached to variants.')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->tooltip('Attributes attached to variants will not be deleted'),
                ]),
            ])

            ->striped()
            ->defaultSort('created_at', 'desc');
    }
}
