<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $school_name;

    public string $school_website;

    public string $title;

    public string $subtitle;

    public static function group(): string
    {
        return 'app';
    }
}
