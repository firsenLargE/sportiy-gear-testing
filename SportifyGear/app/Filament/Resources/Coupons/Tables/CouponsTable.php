<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sn')
                    ->label('S.N.')
                    ->rowIndex(),

                TextColumn::make('code')
                    ->label('Coupon Code')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Coupon code copied')
                    ->weight('bold'),

                TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'success' => 'percentage',
                        'warning' => 'fixed',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                TextColumn::make('value')
                    ->label('Discount')
                    ->formatStateUsing(function ($state, $record) {

                        if (!$record) {
                            return $state;
                        }

                        return $record->type === 'percentage'
                            ? $state . '%'
                            : 'Rs ' . number_format($state, 2);
                    })
                    ->sortable(),

                TextColumn::make('minimum_order_amount')
                    ->money('NPR')
                    ->label('Min Order')
                    ->sortable(),

                TextColumn::make('maximum_discount')
                    ->money('NPR')
                    ->label('Max Discount')
                    ->visible(fn($record) => $record?->type === 'percentage')
                    ->sortable(),

                TextColumn::make('usage')
                    ->label('Usage')
                    ->getStateUsing(function ($record) {

                        if (!$record) {
                            return null;
                        }

                        $used = $record->usages_count ?? 0;
                        $limit = $record->usage_limit ?? '∞';

                        return $used . ' / ' . $limit;
                    }),

                TextColumn::make('usage_per_user')
                    ->label('Per User')
                    ->formatStateUsing(fn($state) => $state ?? '∞'),

                TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('status')
                    ->getStateUsing(function ($record) {

                        if (!$record) {
                            return null;
                        }

                        if (!$record->is_active) {
                            return 'Disabled';
                        }

                        if ($record->starts_at && now()->lt($record->starts_at)) {
                            return 'Scheduled';
                        }

                        if ($record->expires_at && now()->gt($record->expires_at)) {
                            return 'Expired';
                        }

                        return 'Active';
                    })
                    ->badge()
                    ->colors([
                        'success' => 'Active',
                        'warning' => 'Scheduled',
                        'danger' => 'Expired',
                        'gray' => 'Disabled',
                    ]),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Enabled'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Updated')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->groups([
                Group::make('type')
                    ->label('Coupon Type')
                    ->collapsible(),

                Group::make('is_active')
                    ->label('Status')
                    ->getTitleFromRecordUsing(
                        fn($record) => $record->is_active ? 'Active' : 'Inactive'
                    )
                    ->collapsible(),

                Group::make('starts_at')
                    ->label('Start Date')
                    ->collapsible(),

                Group::make('expires_at')
                    ->label('End Date')
                    ->collapsible(),
            ])

            ->defaultGroup('type')

            ->recordActions([

                ActionGroup::make([

                    ViewAction::make(),

                    EditAction::make(),

                    DeleteAction::make()
                        ->disabled(fn($record) => ($record->usages_count ?? 0) > 0)
                        ->tooltip('Cannot delete coupon that has been used'),

                ])
            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                        ->action(function ($records) {

                            $deletable = $records->filter(fn($r) => ($r->usages_count ?? 0) === 0);
                            $skipped   = $records->filter(fn($r) => ($r->usages_count ?? 0) > 0);

                            $deletable->each->delete();

                            if ($skipped->isNotEmpty()) {

                                Notification::make()
                                    ->title('Some coupons were not deleted')
                                    ->body($skipped->count() . ' coupons have been used and were skipped.')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->tooltip('Coupons that have been used cannot be deleted'),
                ])
            ])

            ->defaultSort('created_at', 'desc');
    }
}
