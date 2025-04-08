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
    /**
     * Display a media file by ID.
     *
     * This endpoint returns a file or image from the system. If the media is stored on S3,
     * it redirects to the public URL. If it is an image, it can optionally be resized
     * using query parameters `w` (width) and `h` (height).
     *
     * @OA\Get(
     *     path="/api/media/{media}",
     *     summary="Get media file",
     *     description="Serve or redirect a media file. Resize if image using query params `w` and `h`.",
     *     operationId="getMediaFile",
     *     tags={"Media"},
     *
     *     @OA\Parameter(
     *         name="media",
     *         in="path",
     *         required=true,
     *         description="ID of the media file",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="w",
     *         in="query",
     *         required=false,
     *         description="Optional width for image resizing",
     *
     *         @OA\Schema(type="integer", example=300)
     *     ),
     *
     *     @OA\Parameter(
     *         name="h",
     *         in="query",
     *         required=false,
     *         description="Optional height for image resizing",
     *
     *         @OA\Schema(type="integer", example=200)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Media file served successfully",
     *
     *         @OA\Header(
     *             header="Content-Type",
     *             description="MIME type of the file",
     *
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to S3 public URL"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Media not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Media not found.")
     *         )
     *     )
     * )
     */
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
