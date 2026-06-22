<?php

namespace App\Settings;

use App\Enums\StorageDriver;
use Spatie\LaravelSettings\Attributes\ShouldBeEncrypted;
use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $school_name;

    public string $school_website;

    public string $title;

    public string $subtitle;

    public bool $encryption_enabled = false;

    public string $upload_disk = 'public';

    public function getUploadDiskDriver(): StorageDriver
    {
        return StorageDriver::tryFrom($this->upload_disk) ?? StorageDriver::Local;
    }

    #[ShouldBeEncrypted]
    public ?string $s3_key = null;

    #[ShouldBeEncrypted]
    public ?string $s3_secret = null;

    public ?string $s3_region = null;

    public ?string $s3_bucket = null;

    public ?string $s3_endpoint = null;

    public ?string $s3_url = null;

    public bool $s3_use_path_style_endpoint = false;

    public static function group(): string
    {
        return 'app';
    }

    public function resetToDefaults(): void
    {
        $this->school_name = 'My School';
        $this->school_website = 'https://example.com';
        $this->title = 'Check Information Self Service';
        $this->subtitle = 'Choose a service to check your information quickly and easily.';
        $this->encryption_enabled = false;

        $this->upload_disk = env('FILESYSTEM_DISK', 'public');
        $this->s3_key = env('AWS_ACCESS_KEY_ID');
        $this->s3_secret = env('AWS_SECRET_ACCESS_KEY');
        $this->s3_region = env('AWS_DEFAULT_REGION', 'us-east-1');
        $this->s3_bucket = env('AWS_BUCKET', '');
        $this->s3_endpoint = env('AWS_ENDPOINT', '');
        $this->s3_url = env('AWS_URL', '');
        $this->s3_use_path_style_endpoint = (bool) env('AWS_USE_PATH_STYLE_ENDPOINT', false);

        $this->save();
    }
}
