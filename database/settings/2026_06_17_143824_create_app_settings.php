<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app.school_name', 'My School');
        $this->migrator->add('app.school_website', 'https://example.com');
        $this->migrator->add('app.title', 'Check Information Self Service');
        $this->migrator->add('app.subtitle', 'Choose a service to check your information quickly and easily.');
    }
};
