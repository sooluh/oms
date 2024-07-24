<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Profile extends Page
{
    protected static string $view = 'filament.pages.profile';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Ubah Profil';

    protected function getViewData(): array
    {
        return [
            'user' => Auth::user(),
        ];
    }
}
