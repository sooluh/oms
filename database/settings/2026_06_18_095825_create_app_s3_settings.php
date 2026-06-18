<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app.s3_key', null);
        $this->migrator->add('app.s3_secret', null);
        $this->migrator->add('app.s3_region', 'us-east-1');
        $this->migrator->add('app.s3_bucket', '');
        $this->migrator->add('app.s3_endpoint', '');
        $this->migrator->add('app.s3_url', '');
        $this->migrator->add('app.s3_use_path_style_endpoint', false);
    }
};
