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
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'maps_url' => ['nullable', 'url', 'max:2048'],
            'kecamatan' => ['nullable', 'string', 'max:150'],
            'kota' => ['nullable', 'string', 'max:150'],
            'provinsi' => ['nullable', 'string', 'max:150'],
            'link_maps' => ['nullable', 'url', 'max:2048'],
            'harga_tiket' => ['nullable', 'numeric', 'min:0'],
            'estimasi_transportasi' => ['nullable', 'numeric', 'min:0'],
            'estimasi_biaya_lainnya' => ['nullable', 'numeric', 'min:0'],
            'rating_maps' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'jumlah_rating_maps' => ['nullable', 'integer', 'min:0'],
            'jam_operasional' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'foto_utama' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hotel_terkait' => ['nullable', 'array', 'max:3'],
            'hotel_terkait.*.hotel_id' => ['nullable', 'integer', Rule::exists('hotels', 'id')->whereNull('deleted_at')],
            'hotel_terkait.*.keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
