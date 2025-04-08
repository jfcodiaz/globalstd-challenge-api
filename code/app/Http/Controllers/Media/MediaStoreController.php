<?php

namespace App\Http\Controllers\Media;

use App\Http\Requests\Media\MediaStoreRequest;
use App\Models\Media;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Routing\Controller;

class MediaStoreController extends Controller
{
    /**
     * Upload a media file (image, document, etc.).
     *
     * Stores a file in the configured filesystem and links it to the current user,
     * along with its metadata. Used typically for avatar upload, documents, etc.
     *
     * @OA\Post(
     *     path="/api/media",
     *     summary="Upload a media file",
     *     description="Store a media file and associate it to the authenticated user",
     *     operationId="uploadMedia",
     *     tags={"Media"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"file", "collection"},
     *
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="The file to upload"
     *                 ),
     *                 @OA\Property(
     *                     property="collection",
     *                     type="string",
     *                     example="avatar",
     *                     description="The collection or category of the media (e.g., avatar, resume)"
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Media file uploaded successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="model_type", type="string", example="App\\Models\\User"),
     *             @OA\Property(property="model_id", type="integer", example=1),
     *             @OA\Property(property="collection", type="string", example="avatar"),
     *             @OA\Property(property="disk", type="string", example="local"),
     *             @OA\Property(property="path", type="string", example="uploads/avatar123.jpg"),
     *             @OA\Property(property="mime_type", type="string", example="image/jpeg"),
     *             @OA\Property(property="original_name", type="string", example="profile.jpg")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function __invoke(
        MediaStoreRequest $request,
        FileUploadService $fileUploadService
    ) {
        $user = $request->user()->id;
        $file = $request->file('file');
        $collection = $request->input('collection');
        $disk = config('filesystems.default');

        $path = $fileUploadService(
            file: $file,
            disk: $disk,
            collection: $collection
        )['path'];

        $media = Media::create([
            'model_type' => User::class,
            'model_id' => $user,
            'collection' => $collection,
            'disk' => $disk,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'original_name' => $file->getClientOriginalName(),
        ]);

        return response()->json($media, 201);
    }
}
