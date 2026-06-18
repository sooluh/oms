<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->encrypt('app.s3_key');
        $this->migrator->encrypt('app.s3_secret');
    }
};
