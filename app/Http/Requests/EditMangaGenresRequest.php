<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditMangaGenresRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Auth::user()->isAdmin() || \Auth::user()->isMaintainer();
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
            'genres' => 'required|array',
            'genres.*' => 'required|int|exists:genres,id'
        ];
    }
}
