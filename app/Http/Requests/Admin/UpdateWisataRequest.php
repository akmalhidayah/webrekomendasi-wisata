<?php

namespace App\Http\Requests\Admin;

class UpdateWisataRequest extends StoreWisataRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
