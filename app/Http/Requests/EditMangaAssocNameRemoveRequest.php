<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditMangaAssocNameRemoveRequest extends FormRequest
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
            'manga_id' => 'required|int|exists:manga,id',
            'associated_name_reference_id' => 'required|int|exists:associated_name_references,id',
        ];
    }
}
