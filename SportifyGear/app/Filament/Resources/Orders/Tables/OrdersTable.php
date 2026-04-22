<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending'     => 'gray',
                        'Confirmed'   => 'info',
                        'Processing'  => 'warning',
                        'Shipped'     => 'primary',
                        'Delivered'   => 'success',
                        'Cancelled'   => 'danger',
                        default       => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('NPR')
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Paid'    => 'success',
                        'Pending' => 'warning',
                        'Failed'  => 'danger',
                        default   => 'gray',
                    })
                    ->getStateUsing(function ($record): string {
                        $latestPayment = $record->payments()->latest()->first();
                        return $latestPayment?->status ?? 'Pending';
                    }),

                TextColumn::make('address.city')
                    ->label('Shipping City')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->placeholder('All Statuses'),

                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['to'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                Action::make('viewItems')
                    ->label('View Items')
                    ->icon('heroicon-o-shopping-cart')
                    ->modalHeading(fn($record) => "Order #{$record->order_number} – Items")
                    ->modalContent(fn($record) => view('filament.resources.orders.components.order-items-modal', [
                        'order' => $record,
                    ]))
                    ->modalSubmitAction(false),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
