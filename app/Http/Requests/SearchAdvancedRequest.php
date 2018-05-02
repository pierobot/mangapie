<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchAdvancedRequest extends FormRequest
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
            'type' => 'required|in:advanced',
            'genres' => 'array|nullable|required_without_all:artist,author,keywords|exists:genres,name',
            'artist' => 'nullable|required_without_all:genres,author,keywords|string',
            'author' => 'nullable|required_without_all:genres,artist,keywords|string',
            'keywords' => 'nullable|required_without_all:genres,artist,author|string',
            'page' => 'integer'
        ];
    }
}
