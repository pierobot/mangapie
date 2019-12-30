<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class PutStatusRequest extends FormRequest
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
            'status' => 'required|string|in:completed,dropped,on_hold,planned,reading',
            'manga_id' => 'required|integer|exists:manga,id'
        ];
    }
}
