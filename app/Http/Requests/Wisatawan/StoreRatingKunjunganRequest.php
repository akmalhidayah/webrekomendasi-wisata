<?php

namespace App\Http\Requests\Wisatawan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRatingKunjunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['pernah_dikunjungi' => $this->boolean('pernah_dikunjungi')]);
    }

    public function rules(): array
    {
        return [
            'wisata_id' => [
                'required', 'integer',
                Rule::exists('wisata', 'id')->where(fn ($query) => $query->where('status', 'aktif')->whereNull('deleted_at')),
            ],
            'pernah_dikunjungi' => ['required', 'boolean', 'accepted'],
            'rating' => ['exclude_unless:pernah_dikunjungi,true', 'required', 'integer', 'between:1,5'],
            'ulasan' => ['exclude_unless:pernah_dikunjungi,true', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'pernah_dikunjungi.accepted' => 'Rating hanya dapat diberikan jika Anda pernah mengunjungi destinasi tersebut.',
        ];
    }
}
