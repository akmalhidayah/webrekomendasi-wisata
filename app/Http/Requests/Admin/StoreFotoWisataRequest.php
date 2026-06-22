<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFotoWisataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'path_foto' => ['required', 'array', 'min:1', 'max:10'],
            'path_foto.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'caption' => ['nullable', 'string', 'max:255'],
            'is_utama' => ['nullable', 'boolean'],
        ];
    }
}
