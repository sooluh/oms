<?php

namespace App\Settings;

use App\Settings\Casts\RepeaterCast;
use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $name;

    public string $holder;

    public string $region;

    public string $address;

    public array $email;

    public array $phone;

    public array $social;

    public static function group(): string
    {
        return 'app';
    }

    public static function casts(): array
    {
        return [
            'email' => RepeaterCast::class,
            'phone' => RepeaterCast::class,
            'social' => RepeaterCast::class,
        ];
    }
}
