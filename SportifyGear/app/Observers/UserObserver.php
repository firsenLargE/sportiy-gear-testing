<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Container\Attributes\Auth;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Skip during console/seeding
        if (app()->runningInConsole()) return;

        // Get all admins
        $admins = Admin::all();

        foreach ($admins as $admin) {
            Notification::make()
                ->title('New User Registered')
                ->body("{$user->name} ({$user->email}) just registered.")
                ->success()
                ->actions([
                    Action::make('view')
                        ->url(route('filament.admin.resources.users.edit', $user))
                        ->markAsRead()
                ])
                ->sendToDatabase($admin);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
