<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Admin;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        if (app()->runningInConsole()) return;
        $order->load('user');
        $admins = Admin::all();
        foreach ($admins as $admin) {
            Notification::make()
                ->title('New Order Placed')
                ->body("Order #{$order->order_number} by {$order->user->name} (Total: {$order->total})")
                ->success()
                ->actions([
                    Action::make('view')
                        ->url(route('filament.admin.resources.orders.edit', $order))
                        ->markAsRead()
                ])
                ->sendToDatabase($admin);
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
