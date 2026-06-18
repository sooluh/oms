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
}
