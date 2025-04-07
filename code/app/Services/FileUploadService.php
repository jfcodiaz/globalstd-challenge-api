<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Log\Logger;
use Illuminate\Support\Str;

class FileUploadService
{
    public function __construct(
        private Logger $logger,
        private FilesystemFactory $filesystem,
    ) {}

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
        if (in_array($disk, ['local', 'public']) &&
            function_exists('posix_geteuid') &&
            PHP_OS_FAMILY !== 'Windows') {

            try {
                $absolutePath = $this->filesystem->disk($disk)->path($path);

                $chownSuccess = @chown($absolutePath, 'www-data');
                $chgrpSuccess = @chgrp($absolutePath, 'www-data');

                if ($chownSuccess && $chgrpSuccess) {
                    $this->logger->info("✅ Archivo '{$path}' asignado a www-data correctamente.");
                } else {
                    $this->logger->warning("⚠️ No se pudo asignar www-data como dueño del archivo '{$path}'.");
                }
            } catch (\Throwable $e) {
                $this->logger->error("❌ Error al asignar dueño a '{$path}': ".$e->getMessage());
            }
        }

        return [
            'disk' => $disk,
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'original_name' => $file->getClientOriginalName(),
            'collection' => $collection,
        ];
    }
}
