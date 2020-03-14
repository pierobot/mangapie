<?php

namespace App\Http\Requests\UserSettings;

use Illuminate\Foundation\Http\FormRequest;

class PatchReaderDirectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'direction' => 'required|string|size:3|in:ltr,rtl,vrt'
        ];
    }
}
