<?php

namespace App\Http\Requests\Wisatawan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSurveyPreferensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ratings' => ['required', 'array', 'size:10'],
            'ratings.*.wisata_id' => [
                'required', 'integer', 'distinct',
                Rule::exists('wisata', 'id')->where(fn ($query) => $query
                    ->where('status', 'aktif')
                    ->whereNull('deleted_at')),
            ],
            'ratings.*.rating_awal' => ['required', 'integer', 'between:1,5'],
        ];
    }

    public function messages(): array
    {
        return [
            'ratings.size' => 'Semua 10 destinasi harus diberi rating.',
            'ratings.*.wisata_id.distinct' => 'Destinasi pada survei tidak boleh duplikat.',
            'ratings.*.rating_awal.required' => 'Semua destinasi harus diberi rating.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $submitted = collect($this->input('ratings', []))->pluck('wisata_id')->map(fn ($id) => (int) $id)->sort()->values();
            $expected = collect($this->session()->get('survey_wisata_ids', []))->map(fn ($id) => (int) $id)->sort()->values();

            if ($expected->count() !== 10 || $submitted->all() !== $expected->all()) {
                $validator->errors()->add('ratings', 'Destinasi survei tidak sesuai dengan sesi. Muat ulang halaman survei.');
            }
        });
    }
}
