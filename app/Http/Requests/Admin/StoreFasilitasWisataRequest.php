<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFasilitasWisataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'nama_fasilitas' => ['required', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string'],
        ];
    }
}
