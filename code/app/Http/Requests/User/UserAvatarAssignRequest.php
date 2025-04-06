<?php

namespace App\Http\Requests\User;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;

class UserAvatarAssignRequest extends FormRequest
{
    protected ?Media $media = null;

    public function authorize(): bool
    {
        $mediaId = $this->input('media_id');

        if (! $mediaId) {
            return true;
        }
        /** @var Media */
        $media = Media::find($mediaId);

        if (! $media) {
            return true;
        }

        $this->media = $media;

        return $media->model_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'media_id' => ['required', 'exists:media,id'],
        ];
    }

    public function passedValidation()
    {
        $this->merge(['_media' => $this->media]);
    }
}
