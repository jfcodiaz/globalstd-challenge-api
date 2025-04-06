<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadService
{
    public function __invoke(
        UploadedFile $file,
        string $collection = 'default',
        ?string $disk = null
    ): array {
        $disk ??= config('filesystems.default', 'local');

        $directory = 'uploads/'.now()->format('Y/m');

        $filename = Str::random(40).'.'.
                    $file->getClientOriginalExtension();

        $path = $file->storeAs($directory, $filename, $disk);

        return [
            'disk' => $disk,
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'original_name' => $file->getClientOriginalName(),
            'collection' => $collection,
        ];
    }
}
