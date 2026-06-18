<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum StorageDriver: string implements HasDescription, HasLabel
{
    case Local = 'public';
    case S3 = 's3';
    case Database = 'database';

    public function getLabel(): string
    {
        return match ($this) {
            self::Local => 'Local File System',
            self::S3 => 'S3-Compatible Storage',
            self::Database => 'Database',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Local => 'Store new attachments on the server file system. This is the default for new instances.',
            self::S3 => 'Store new attachments in an S3-compatible object storage service.',
            self::Database => 'Store attachment blobs directly in the application database.',
        };
    }
}
