<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    public function attachments(Request $request, string $path)
    {
        /** @var Storage $disk */
        $disk = Storage::disk('database');
        abort_unless($disk->exists($path), 404);

        return response($disk->get($path), 200, [
            'Content-Type' => $disk->mimeType($path),
        ]);
    }
}
