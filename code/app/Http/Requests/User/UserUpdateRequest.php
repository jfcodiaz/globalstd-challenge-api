<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $targetUser = $this->route('user');

        if ($user->hasAnyRole(['SuperAdmin', 'Admin'])) {
            return true;
        }

        return $user->id === $targetUser->id;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('user')->id),
            ],
            'password' => 'sometimes|required|string|min:6',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ];
    }
}
