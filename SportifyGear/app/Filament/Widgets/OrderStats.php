<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Status;
use Carbon\Carbon;

class OrderStats extends StatsOverviewWidget
{
    protected int | array | null $columns = 3;

    protected function getStats(): array
    {
        $statuses = Status::all();
        $stats = [];

        $totalOrders = Order::count();

        $totalOrdersTrend = [];
        $runningTotal = Order::whereDate('created_at', '<=', Carbon::now()->subDays(6))->count();

        foreach (range(6, 0) as $day) {
            $daily = Order::whereDate('created_at', Carbon::now()->subDays($day))->count();
            $runningTotal += $daily;
            $totalOrdersTrend[] = $runningTotal;
        }

        $stats[] = Stat::make('Total Orders', number_format($totalOrders))
            ->description('All orders')
            ->descriptionIcon('heroicon-m-chart-bar')
            ->icon('heroicon-o-shopping-cart')
            ->color('primary')
            ->chart($totalOrdersTrend);

        $deliveredStatus = $statuses->firstWhere('name', 'Delivered');

        $deliveredRevenue = $deliveredStatus
            ? Order::where('status_id', $deliveredStatus->id)->sum('total')
            : 0;

        $stats[] = Stat::make(
            'Total Revenue',
            'Rs ' . number_format($deliveredRevenue, 2)
        )
            ->description('Completed orders')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->chart(
                collect(range(6, 0))->map(function ($day) use ($deliveredStatus) {
                    return $deliveredStatus
                        ? Order::where('status_id', $deliveredStatus->id)
                        ->whereDate('created_at', Carbon::now()->subDays($day))
                        ->sum('total')
                        : 0;
                })->toArray()
            );


        foreach ($statuses as $status) {

            $count = Order::where('status_id', $status->id)->count();
            $amount = Order::where('status_id', $status->id)->sum('total');

            $trend = collect(range(6, 0))->map(function ($day) use ($status) {
                return Order::where('status_id', $status->id)
                    ->whereDate('created_at', Carbon::now()->subDays($day))
                    ->count();
            })->toArray();

            $color = match ($status->name) {
                'Pending', 'Confirmed', 'Processing', 'Shipped' => 'warning',
                'Delivered' => 'success',
                'Cancelled', 'Returned' => 'danger',
                default => 'gray',
            };


            $icon = match ($status->name) {
                'Pending' => 'heroicon-o-clock',
                'Confirmed' => 'heroicon-o-check',
                'Processing' => 'heroicon-o-cog',
                'Shipped' => 'heroicon-o-truck',
                'Delivered' => 'heroicon-o-check-circle',
                'Cancelled' => 'heroicon-o-x-circle',
                'Returned' => 'heroicon-o-arrow-uturn-left',
                default => 'heroicon-o-document-text',
            };

            $stats[] = Stat::make(
                $status->name,
                'Rs ' . number_format($amount, 2)
            )
                ->description(number_format($count) . ' orders')
                ->descriptionIcon($icon)
                ->icon($icon)
                ->color($color)
                ->chart($trend);
        }

        return $stats;
    }
}
