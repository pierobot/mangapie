<?php

namespace App\Http\Requests\Edit\Cover;

use Illuminate\Foundation\Http\FormRequest;

class CoverUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = \Auth::user();

        return $user->hasRole('Administrator') || $user->hasRole('Editor');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'manga_id' => 'required|integer|exists:manga,id',
            'archive_id' => 'required|integer|exists:archives,id',
            'page' => 'required|integer'
        ];
    }
}
