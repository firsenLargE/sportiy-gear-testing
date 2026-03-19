<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use App\Models\Category;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sn')
                    ->label('S.N.')
                    ->rowIndex(),

                TextColumn::make('parent.name')
                    ->label('Parent Category')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-folder')
                    ->placeholder('Main Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Category Name')
                    ->icon('heroicon-o-tag')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->copyable()
                    ->icon('heroicon-o-link')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->slug)
                    ->searchable(),

                TextColumn::make('products_count')
                    ->label('Total Products')
                    ->counts('products')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->label('Parent Category')
                    ->options(
                        Category::pluck('name', 'id')
                    ),
            ])
            ->groups([
                Group::make('parent_id')
                    ->label('Parent Category')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => $record->parent?->name ?? 'Main Category'),

                Group::make('created_at')
                    ->label('Created Date')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => $record->created_at?->format('Y-m-d') ?? 'N/A'),
            ])
            ->defaultGroup('parent_id')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn($record) => $record->products()->exists())
                    ->tooltip('Cannot delete category with products'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            $deletable = $records->filter(fn($record) => !$record->products()->exists());
                            $skipped   = $records->filter(fn($record) => $record->products()->exists());

                            $deletable->each->delete();

                            if ($skipped->isNotEmpty()) {
                                Notification::make()
                                    ->title('Some categories were not deleted')
                                    ->body($skipped->count() . ' categories have products and were skipped.')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->tooltip('Categories with products will not be deleted'),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Search category name or slug...');
    }
}
