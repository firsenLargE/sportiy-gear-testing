<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class UsersTable
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

                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),

                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('phone_no')
                    ->searchable(),

                TextColumn::make('gender')
                    ->badge()
                    ->sortable(),

                TextColumn::make('account_status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->groups([
                Group::make('gender')
                    ->label('Gender'),

                Group::make('account_status')
                    ->label('Account Status'),

                Group::make('email_verified_at')
                    ->label('Email Verified')
                    ->getTitleFromRecordUsing(
                        fn($record) =>
                        $record->email_verified_at ? 'Verified' : 'Not Verified'
                    ),

                Group::make('created_at')
                    ->date()
                    ->label('Registration Date'),
            ])

            ->defaultGroup('account_status')

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
