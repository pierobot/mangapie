<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditMangaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = \Auth::user();

        return $user->isAdmin() == true || $user->isMaintainer() == true;
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
            'action' => 'required|string|in:description.update,description.delete,type.update,type.delete,assoc_name.add,assoc_name.delete,genre.add,genre.delete,author.add,author.delete,artist.add,artist.delete,year.update,year.delete',

            'description' => 'required_if:action,description.update|string',
            'type' => 'required_if:action,type.update|string|in:Manga,Doujinshi,Manwha',
            'assoc_name' => 'required_if:action,assoc_name.add,assoc_name.delete|string',
            'genre' => 'required_if:action,genre.add,genre.delete|string|exists:genres,name',
            'author' => 'required_if:action,author.add,author.delete|string',
            'artist' => 'required_if:action,artist.add,artist.delete|string',
            'year' => ['required_if:action,year.add', 'regex:/^\d{1,4}$/']
        ];
    }
}
