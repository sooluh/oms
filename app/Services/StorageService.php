<?php

namespace App\Services;

use App\Enums\StorageDriver;
use App\Settings\AppSettings;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class StorageService
{
    private AppSettings $settings;

    public function __construct()
    {
        $this->settings = app(AppSettings::class);
    }

    public function diskName(): string
    {
        return $this->settings->upload_disk;
    }

    public function disk(?string $diskName = null): FilesystemAdapter
    {
        $disk = $diskName ?? $this->diskName();

        if ($disk !== StorageDriver::S3->value) {
            return Storage::disk($disk);
        }

        $this->configureS3Disk();

        return Storage::disk($disk);
    }

    public function url(string $path, ?string $recordDisk = null): string
    {
        $disk = $recordDisk ?? $this->diskName();

        return $this->disk($disk)->url($path);
    }

    public function store(string $path, string|UploadedFile $contents): string
    {
        $disk = $this->disk();
        $disk->put($path, $contents);

        return $path;
    }

    public function delete(string $path, ?string $diskName = null): void
    {
        $this->disk($diskName)->delete($path);
    }

    private function configureS3Disk(): void
    {
        Config::set('filesystems.disks.s3', [
            'driver' => 's3',
            'key' => $this->settings->s3_key,
            'secret' => $this->settings->s3_secret,
            'region' => $this->settings->s3_region ?? 'us-east-1',
            'bucket' => $this->settings->s3_bucket,
            'url' => $this->settings->s3_url,
            'endpoint' => $this->settings->s3_endpoint,
            'use_path_style_endpoint' => $this->settings->s3_use_path_style_endpoint,
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ]);
    }
}
