<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {

        Gate::define('room-role', function (User $user, Room $room, ...$roles) {
            $roomUser = $user->rooms()->where('rooms.id', $room->id)->first();
            if (!$roomUser) {
                return false;
            }
            return in_array($roomUser->pivot->role, $roles);
        });

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $rooms = $user->rooms;
                $view->with('sidebarRooms', $rooms);
            }
        });
    }
}
