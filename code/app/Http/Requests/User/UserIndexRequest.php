<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo SuperAdmin y Admin pueden listar usuarios
        return $this->user()->hasAnyRole(['SuperAdmin', 'Admin', 'Employee']);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'role' => ['nullable', 'string', 'in:Admin,Client,Employee,SuperAdmin'],
            'is_active' => ['nullable', 'in:0,1'],
        ];
    }
}
