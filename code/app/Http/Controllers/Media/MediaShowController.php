<?php

namespace App\Http\Controllers\Media;

use App\Http\Requests\Media\MediaShowRequest;
use App\Models\Media;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MediaShowController extends Controller
{
    public function __invoke(MediaShowRequest $request, Media $media)
    {
        $disk = $media->disk ?? config('filesystems.default');
        $storage = Storage::disk($disk);
        $isImage = str_starts_with($media->mime_type, 'image/');
        $width = $request->input('w');
        $height = $request->input('h');
        $useCache = config('app.cache_media', false);

        // S3 redirection
        if ($disk === 's3') {
            $url = $storage->url($media->path);

            return redirect()->away($url);
        }

        // File not found
        if (! $storage->exists($media->path)) {
            return response()->json(['message' => 'Media not found.'], 404);
        }

        $path = $media->path;
        // Resize image if applicable
        if ($isImage && ($width || $height)) {
            $resizedPath = "resized/{$width}x{$height}/{$media->path}";

            if (! $storage->exists($resizedPath)) {
                $manager = new ImageManager(new Driver);

                $image = $manager->read($storage->path($media->path))
                    ->scale(width: $width, height: $height);

                $storage->put($resizedPath, (string) $image->toJpeg());
            }

            $path = $resizedPath;
        }

        $file = $storage->get($path);
        $etag = hash('sha512', $file);

        return Response::make($file, 200, [
            'Content-Type' => $media->mime_type,
            'Cache-Control' => $useCache ? 'max-age=31536000, public' : 'no-cache',
            'ETag' => $etag,
        ]);
    }
}
