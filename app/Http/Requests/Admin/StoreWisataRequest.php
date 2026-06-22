<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWisataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'kota' => $this->input('kota') ?: 'Makassar',
            'provinsi' => $this->input('provinsi') ?: 'Sulawesi Selatan',
        ]);
    }

    public function rules(): array
    {
        return [
            'kategori_wisata_id' => ['required', Rule::exists('kategori_wisata', 'id')],
            'nama_wisata' => ['required', 'string', 'max:200'],
            'jenis_wisata' => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'alamat' => ['required', 'string'],
            'kecamatan' => ['nullable', 'string', 'max:150'],
            'kota' => ['nullable', 'string', 'max:150'],
            'provinsi' => ['nullable', 'string', 'max:150'],
            'link_maps' => ['nullable', 'url'],
            'harga_tiket' => ['nullable', 'numeric', 'min:0'],
            'estimasi_transportasi' => ['nullable', 'numeric', 'min:0'],
            'estimasi_biaya_lainnya' => ['nullable', 'numeric', 'min:0'],
            'jam_operasional' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'foto_utama' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
