<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'nama_hotel' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'harga_min' => ['required', 'numeric', 'min:0'],
            'harga_max' => ['nullable', 'numeric', 'min:0', 'gte:harga_min'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'traveloka_url' => ['nullable', 'url', 'max:2048'],
            'maps_url' => ['nullable', 'url', 'max:2048'],
            'rating_hotel' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ];
    }

    public function messages(): array
    {
        return [
            'gambar.image' => 'File gambar hotel harus berupa gambar yang valid.',
            'gambar.mimes' => 'Format gambar hotel harus JPG, JPEG, PNG, atau WebP.',
            'gambar.max' => 'Ukuran gambar hotel maksimal 2 MB.',
            'harga_max.gte' => 'Harga maksimum tidak boleh lebih kecil dari harga minimum.',
        ];
    }
}
