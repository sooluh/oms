<?php

namespace App\Settings\Casts;

use Spatie\LaravelSettings\SettingsCasts\SettingsCast;

class RepeaterCast implements SettingsCast
{
    public function get($payload): array
    {
        return json_decode($payload, true);
    }

    public function set($payload): string
    {
        return json_encode($payload);
    }
}
