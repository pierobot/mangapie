<?php

namespace App\Http\Requests\UserSettings;

use Illuminate\Foundation\Http\FormRequest;

class PutAboutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'about' => 'string|nullable'
        ];
    }
}
