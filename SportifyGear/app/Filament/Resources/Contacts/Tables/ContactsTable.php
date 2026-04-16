<?php

namespace App\Filament\Resources\Contacts\Tables;

use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('User')
                    ->icon('heroicon-o-user')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('email')
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('message')
                    ->label('Message')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->message),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Read' : 'Pending')
                    ->color(fn($state) => $state ? 'success' : 'warning')
                    ->icon(
                        fn($state) => $state
                            ? 'heroicon-o-check-circle'
                            : 'heroicon-o-clock'
                    ),

                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable()
                    ->tooltip(fn($record) => $record->created_at->format('d M Y, h:i A')),
            ])

            ->groups([
                Group::make('status')
                    ->label('Status')
                    ->getTitleFromRecordUsing(
                        fn($record) =>
                        $record->status ? 'Read Messages' : 'Pending Messages'
                    ),
            ])
            ->defaultGroup('status')

            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        0 => 'Pending',
                        1 => 'Read',
                    ]),
            ])

            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                Action::make('markAsRead')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn($record) => !$record->status)
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 1])),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
