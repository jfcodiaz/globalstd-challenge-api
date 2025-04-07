<?php

namespace App\Http\Controllers\Media;

use App\Http\Requests\Media\MediaStoreRequest;
use App\Models\Media;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Routing\Controller;

class MediaStoreController extends Controller
{
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
