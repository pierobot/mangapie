<?php

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;

class DeleteArchiveNotificationsRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge([
            'series' => json_decode($this->get('series'))
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'series' => 'array|required',
            'series.*' => 'int|exists:manga,id'
        ];
    }
}
