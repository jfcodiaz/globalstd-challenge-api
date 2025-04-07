<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserAvatarSeeder extends Seeder
{
    public function run(): void
    {
        $path = Storage::disk('fixtures')
            ->path('images/avatar_mobile.jpg');

        $file = new UploadedFile(
            $path,
            'avatar.jpg',
            'image/jpeg',
            null,
            true
        );

        $uploadService = app(FileUploadService::class);

        User::all()->each(function (User $user) use ($file, $uploadService) {
            $attributes = $uploadService($file, 'avatar', 'local');

            Media::create([
                ...$attributes,
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
        });
    }
}
