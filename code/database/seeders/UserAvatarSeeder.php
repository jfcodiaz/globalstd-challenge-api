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
        // Fix rápido: asignar www-data como dueño recursivamente al directorio 'uploads'
        // queda pediente hacerlo de una mejor forma
        $this->fixUploadPermissions();

        $path = Storage::disk('fixtures')->path('images/avatar_mobile.jpg');

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

            $media = Media::create([
                ...$attributes,
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);

            $user->update([
                'avatar_id' => $media->id,
            ]);
        });
    }

    protected function fixUploadPermissions(): void
    {
        $disk = 'local';

        if (! in_array($disk, ['local', 'public'])) {
            return;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            return;
        }

        $path = Storage::disk($disk)->path('uploads');

        if (file_exists($path)) {
            exec('chown -R www-data:www-data '.escapeshellarg($path));
        }
    }
}
