<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKategoriWisataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'nama_kategori' => ['required', 'string', 'max:150', Rule::unique('kategori_wisata', 'nama_kategori')],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
