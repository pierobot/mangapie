<?php

namespace App\Http\Requests\Library;

use Illuminate\Foundation\Http\FormRequest;

class LibraryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->admin == true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'library_id' => 'required|int|exists:libraries,id',
            'action' => 'required|string|in:rename,refresh',
            'name' => 'required_if:action,rename|string|nullable|unique:libraries,name'
        ];
    }
}
