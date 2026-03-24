<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use Carbon\Carbon;

class UserStats extends StatsOverviewWidget
{

    protected function getStats(): array
    {

        $totalUsers = User::count();
        $registeredUsers = User::whereNotNull('email_verified_at')->count();
        $notRegisteredUsers = User::whereNull('email_verified_at')->count();

        $last7Days = User::whereDate('created_at', '>=', Carbon::now()->subDays(6))->count();
        $prev7Days = User::whereBetween('created_at', [
            Carbon::now()->subDays(13),
            Carbon::now()->subDays(7),
        ])->count();

        $growth = $prev7Days > 0 ? (($last7Days - $prev7Days) / $prev7Days) * 100 : ($last7Days > 0 ? 100 : 0);

        $growthDescription = $growth > 0
            ? round($growth, 1) . '% growth (7d)'
            : ($growth < 0 ? round(abs($growth), 1) . '% decline (7d)' : 'Stable (7d)');

        $growthIcon = $growth > 0
            ? 'heroicon-m-arrow-trending-up'
            : ($growth < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus');

        $growthColor = $growth > 0 ? 'success' : ($growth < 0 ? 'danger' : 'gray');

        $totalTrend = [];
        $runningTotal = User::whereDate('created_at', '<=', Carbon::now()->subDays(6))->count();
        foreach (range(6, 0) as $day) {
            $date = Carbon::now()->subDays($day);
            $daily = User::whereDate('created_at', $date)->count();
            $runningTotal += $daily;
            $totalTrend[] = $runningTotal;
        }

        $registeredTrend = [];
        $registeredTotal = User::whereNotNull('email_verified_at')
            ->whereDate('email_verified_at', '<=', Carbon::now()->subDays(6))
            ->count();
        foreach (range(6, 0) as $day) {
            $date = Carbon::now()->subDays($day);
            $daily = User::whereNotNull('email_verified_at')
                ->whereDate('email_verified_at', $date)
                ->count();
            $registeredTotal += $daily;
            $registeredTrend[] = $registeredTotal;
        }

        $notRegisteredTrend = collect(range(6, 0))->map(function ($day) {
            return User::whereNull('email_verified_at')
                ->whereDate('created_at', Carbon::now()->subDays($day))
                ->count();
        })->toArray();


        return [

            Stat::make('Total Users', number_format($totalUsers))
                ->description($growthDescription)
                ->descriptionIcon($growthIcon)
                ->icon('heroicon-o-users')
                ->color($growthColor)
                ->chart($totalTrend),

            Stat::make('Registered Users', number_format($registeredUsers))
                ->description('Verified users')
                ->descriptionIcon('heroicon-m-check-badge')
                ->icon('heroicon-o-user-group')
                ->color('success')
                ->chart($registeredTrend),

            Stat::make('Not Registered', number_format($notRegisteredUsers))
                ->description('Pending verification')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->chart($notRegisteredTrend),
        ];
    }
}
