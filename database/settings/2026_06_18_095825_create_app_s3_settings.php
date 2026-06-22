<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app.s3_key', env('AWS_ACCESS_KEY_ID'));
        $this->migrator->add('app.s3_secret', env('AWS_SECRET_ACCESS_KEY'));
        $this->migrator->add('app.s3_region', env('AWS_DEFAULT_REGION', 'us-east-1'));
        $this->migrator->add('app.s3_bucket', env('AWS_BUCKET', ''));
        $this->migrator->add('app.s3_endpoint', env('AWS_ENDPOINT', ''));
        $this->migrator->add('app.s3_url', env('AWS_URL', ''));
        $this->migrator->add('app.s3_use_path_style_endpoint', (bool) env('AWS_USE_PATH_STYLE_ENDPOINT', false));
    }
};
