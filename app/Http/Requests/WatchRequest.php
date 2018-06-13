<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WatchRequest extends FormRequest
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
            'id' => 'required|integer|exists:manga',
            'action' => 'required|string|regex:/^(un)?watch$/'
        ];
    }
}
