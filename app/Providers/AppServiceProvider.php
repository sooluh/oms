<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\User;
use App\Settings\AppSettings;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        /**
         * Share app settings to global blade
         */
        View::share('app', app(AppSettings::class));

        /**
         * Set filament color
         */
        FilamentColor::register([
            'primary' => Color::Green,
        ]);

        /**
         * Auto set enhancer ID
         */
        User::creating(fn ($user) => $user->enhancer_id = auth()->id());
        Page::creating(fn ($user) => $user->enhancer_id = auth()->id());
    }
}
