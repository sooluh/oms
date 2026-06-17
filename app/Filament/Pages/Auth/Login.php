<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        if (config('app.is_demo')) {
            $this->form->fill([
                'email' => 'admin@oms.my.id',
                'password' => 'ohmyschool',
            ]);
        }
    }
}
