<?php

namespace App\Providers;

use App\Filesystem\DatabaseStorageAdapter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('database', function ($_app, $_config) {
            $adapter = new DatabaseStorageAdapter;

            return new FilesystemAdapter(
                new Filesystem($adapter),
                $adapter,
            );
        });
    }
}
