<?php

namespace App\Http\Requests\Response;

use Illuminate\Foundation\Http\FormRequest;

class StoreResponseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content' => 'required|string|max:2000'
        ];
    }
}
