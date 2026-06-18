<?php

namespace App\Filesystem;

use App\Models\Attachment;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathNormalizer;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\WhitespacePathNormalizer;

class DatabaseStorageAdapter implements FilesystemAdapter
{
    private PathNormalizer $normalizer;

    public function __construct()
    {
        $this->normalizer = new WhitespacePathNormalizer;
    }

    public function fileExists(string $path): bool
    {
        return Attachment::where('path', $this->normalizer->normalizePath($path))->exists();
    }

    public function directoryExists(string $path): bool
    {
        return Attachment::where('path', 'like', $this->normalizer->normalizePath($path).'/%')->exists();
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $path = $this->normalizer->normalizePath($path);

        try {
            Attachment::updateOrCreate(
                ['path' => $path],
                [
                    'contents' => $contents,
                    'mime_type' => $config->get('mime_type', 'application/octet-stream'),
                    'size' => strlen($contents),
                ]
            );
        } catch (\Throwable $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage());
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, stream_get_contents($contents), $config);
    }

    public function read(string $path): string
    {
        $path = $this->normalizer->normalizePath($path);
        $record = Attachment::where('path', $path)->first();

        if (! $record) {
            throw UnableToReadFile::fromLocation($path);
        }

        return $record->contents;
    }

    public function readStream(string $path)
    {
        $contents = $this->read($path);
        $stream = fopen('php://temp', 'w+b');

        fwrite($stream, $contents);
        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $path = $this->normalizer->normalizePath($path);
        $deleted = Attachment::where('path', $path)->delete();

        if (! $deleted) {
            throw UnableToDeleteFile::atLocation($path, 'File not found');
        }
    }

    public function deleteDirectory(string $path): void
    {
        $path = $this->normalizer->normalizePath($path);

        Attachment::query()
            ->where('path', 'like', $path.'/%')
            ->orWhere('path', $path)
            ->delete();
    }

    public function createDirectory(string $path, Config $config): void
    {
        // No-op: directories are virtual in database storage
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // No-op: all database files are private by default
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($this->normalizer->normalizePath($path), null, null, null, 'private');
    }

    public function mimeType(string $path): FileAttributes
    {
        $path = $this->normalizer->normalizePath($path);
        $record = Attachment::where('path', $path)->first();

        if (! $record) {
            throw UnableToRetrieveMetadata::mimeType($path);
        }

        return new FileAttributes($path, null, null, null, $record->mime_type ?? 'application/octet-stream');
    }

    public function lastModified(string $path): FileAttributes
    {
        $path = $this->normalizer->normalizePath($path);
        $record = Attachment::where('path', $path)->first();

        if (! $record) {
            throw UnableToRetrieveMetadata::lastModified($path);
        }

        return new FileAttributes($path, null, null, $record->updated_at?->timestamp ?? $record->created_at->timestamp);
    }

    public function fileSize(string $path): FileAttributes
    {
        $path = $this->normalizer->normalizePath($path);
        $record = Attachment::where('path', $path)->first();

        if (! $record) {
            throw UnableToRetrieveMetadata::fileSize($path);
        }

        return new FileAttributes($path, $record->size);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $path = $this->normalizer->normalizePath($path);
        $query = Attachment::query();

        if ($deep) {
            $query->where('path', 'like', $path.'/%');
        } else {
            $query
                ->where('path', 'like', $path.'/%')
                ->where('path', 'not like', $path.'/%/%');
        }

        /** @var Attachment $record */
        foreach ($query->cursor() as $record) {
            yield new FileAttributes(
                $record->path,
                $record->size,
                'private',
                $record->updated_at?->timestamp ?? $record->created_at->timestamp,
                $record->mime_type ?? 'application/octet-stream',
            );
        }

        yield new DirectoryAttributes($path);
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $source = $this->normalizer->normalizePath($source);
        $destination = $this->normalizer->normalizePath($destination);
        $record = Attachment::where('path', $source)->first();

        if (! $record) {
            throw UnableToMoveFile::fromLocationTo($source, $destination);
        }

        try {
            $record->update(['path' => $destination]);
        } catch (\Throwable $e) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $e);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $source = $this->normalizer->normalizePath($source);
        $destination = $this->normalizer->normalizePath($destination);
        $record = Attachment::where('path', $source)->first();

        if (! $record) {
            throw UnableToCopyFile::fromLocationTo($source, $destination);
        }

        try {
            Attachment::create([
                'path' => $destination,
                'contents' => $record->contents,
                'mime_type' => $record->mime_type,
                'size' => $record->size,
            ]);
        } catch (\Throwable $e) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $e);
        }
    }

    public function getUrl(string $path): string
    {
        return route('attachments.show', ['path' => $this->normalizer->normalizePath($path)]);
    }
}
