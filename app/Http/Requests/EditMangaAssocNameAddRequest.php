<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditMangaAssocNameAddRequest extends FormRequest
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
            'name' => 'required|string',
        ];
    }
}
