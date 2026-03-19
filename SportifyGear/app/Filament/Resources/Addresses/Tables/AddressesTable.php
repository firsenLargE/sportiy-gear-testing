<?php

namespace App\Filament\Resources\Addresses\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;

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
                    ->searchable()
                    ->weight('bold')
                    ->description(fn($record) => $record->user?->email)
                    ->limit(40)
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Receiver Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone_no')
                    ->label('Phone Number')
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('province')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('district')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address_line1')
                    ->label('Address')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->address_line1)
                    ->searchable(),

                TextColumn::make('nearest_landmark')
                    ->label('Landmark')
                    ->limit(30)
                    ->toggleable(),

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
            ->groups([
                Group::make('user.name')
                    ->label('User')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => $record->user?->name ?? 'Unknown'),

                Group::make('province')
                    ->label('Province')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => $record->province ?? 'N/A'),

                Group::make('district')
                    ->label('District')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => $record->district ?? 'N/A'),

                Group::make('created_at')
                    ->label('Created Date')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => $record->created_at?->format('Y-m-d') ?? 'N/A'),
            ])
            ->defaultGroup('user.name')

            ->filters([

                SelectFilter::make('province')
                    ->label('Province'),

                SelectFilter::make('district')
                    ->label('District'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn($query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn($query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc');
    }
}
