<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('created_at', 'desc')

            ->columns([

                TextColumn::make('sn')
                    ->label('S.N.')
                    ->rowIndex(),

                ImageColumn::make('images.image_path')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(1)
                    ->size(45)
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->slug)
                    ->limit(35),

                TextColumn::make('categories')
                    ->label('Categories')
                    ->badge()
                    ->color('primary')
                    ->limitList(2)
                    ->getStateUsing(
                        fn($record) =>
                        $record->categories->pluck('name')->toArray()
                    )
                    ->tooltip(
                        fn($record) =>
                        $record->categories->pluck('name')->join(', ')
                    ),

                TextColumn::make('attributes')
                    ->label('Attributes')
                    ->badge()
                    ->color('info')
                    ->limitList(2)
                    ->getStateUsing(function ($record) {

                        $attributes = [];

                        foreach ($record->variants as $variant) {
                            foreach ($variant->attributeValues as $attrValue) {
                                $attributes[] =
                                    $attrValue->attribute->name .
                                    ': ' .
                                    $attrValue->value;
                            }
                        }

                        return array_unique($attributes);
                    })
                    ->tooltip(function ($record) {

                        $attributes = [];

                        foreach ($record->variants as $variant) {
                            foreach ($variant->attributeValues as $attrValue) {
                                $attributes[] =
                                    $attrValue->attribute->name .
                                    ': ' .
                                    $attrValue->value;
                            }
                        }

                        return collect($attributes)->unique()->join(', ');
                    }),

                TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Variants')
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->toggleable(),

                TextColumn::make('admin.name')
                    ->label('Manager')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),

                TextColumn::make('meta_title')
                    ->label('Meta Title')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->meta_title)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])


            ->groups([

                Group::make('admin.name')
                    ->label('Manager')
                    ->collapsible(),

                Group::make('name')
                    ->label('Product Name')
                    ->collapsible(),

                Group::make('is_active')
                    ->label('Status')
                    ->getTitleFromRecordUsing(
                        fn($record) =>
                        $record->is_active ? 'Active' : 'Inactive'
                    )
                    ->collapsible(),

                Group::make('created_at')
                    ->label('Created Date')
                    ->date()
                    ->collapsible(),

            ])

            ->defaultGroup('name')

            ->filters([

                SelectFilter::make('status')
                    ->label('Product Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ])
                    ->attribute('is_active'),

                SelectFilter::make('manager')
                    ->relationship('admin', 'name'),

                SelectFilter::make('category')
                    ->relationship('categories', 'name'),

            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->icon('heroicon-o-pencil-square'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
