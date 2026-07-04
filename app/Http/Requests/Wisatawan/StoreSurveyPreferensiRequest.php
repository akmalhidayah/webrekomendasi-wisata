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
            'ratings.*.rating_awal' => ['required', 'integer', 'min:1', 'max:5'],
            'budget_min' => ['required', 'numeric', 'min:0'],
            'budget_max' => ['required', 'numeric', 'min:0', 'gte:budget_min'],
            'butuh_hotel' => ['required', 'boolean'],
            'jumlah_malam' => ['nullable', 'required_if:butuh_hotel,1', 'integer', 'min:1', 'max:30'],
            'user_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'user_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_location_allowed' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'ratings.size' => 'Semua 10 destinasi harus diberi rating.',
            'ratings.*.wisata_id.distinct' => 'Destinasi pada survei tidak boleh duplikat.',
            'ratings.*.rating_awal.required' => 'Semua destinasi harus diberi rating.',
            'budget_min.required' => 'Budget minimum wajib diisi.',
            'budget_max.required' => 'Budget maksimum wajib diisi.',
            'budget_max.gte' => 'Budget maksimum tidak boleh lebih kecil dari budget minimum.',
            'jumlah_malam.required_if' => 'Jumlah malam wajib diisi jika membutuhkan hotel.',
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
