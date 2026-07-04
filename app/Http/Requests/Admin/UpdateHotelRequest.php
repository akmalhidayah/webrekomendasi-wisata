<?php

namespace App\Http\Requests\Admin;

class UpdateHotelRequest extends StoreHotelRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
