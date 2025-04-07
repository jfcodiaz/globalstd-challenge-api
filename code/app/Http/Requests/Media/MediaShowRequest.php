<?php

namespace App\Http\Requests\Media;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class MediaShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'w' => ['nullable', 'integer', 'min:1'],
            'h' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function passedValidation(): void
    {
        $media = $this->route('media');

        if (! $media instanceof Media || ! str_starts_with($media->mime_type, 'image/')) {
            throw ValidationException::withMessages([
                'media' => ['Only image files can be resized or previewed.'],
            ]);
        }
    }
}
