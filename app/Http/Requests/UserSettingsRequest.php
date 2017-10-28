<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSettingsRequest extends FormRequest
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
            'old-password' => 'string',
            'new-password' => 'string',
            'confirm-password' => 'same:new-password',
            'theme' => 'regex:/\w+\/\w+/',
            'action' => 'required|string|in:password.update,theme.update'
        ];
    }
}
