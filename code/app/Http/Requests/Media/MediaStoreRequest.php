<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class MediaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $collection = $this->input('collection');

        $rules = [
            'collection' => ['required', 'string'],
        ];

        if ($collection === 'avatar') {
            $rules['file'] = ['required', 'file', 'mimes:jpg,jpeg,png'];
        } else {
            $rules['file'] = ['required', 'file'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Only jpg and png files are allowed for avatar uploads.',
        ];
    }
}
