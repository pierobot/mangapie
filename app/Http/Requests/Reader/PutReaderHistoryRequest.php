<?php

namespace App\Http\Requests\Reader;

use Illuminate\Foundation\Http\FormRequest;

class PutReaderHistoryRequest extends FormRequest
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
            'manga_id' => 'required|integer|exists:manga,id',
            'archive_id' => 'required|integer|exists:archives,id',
            'page' => 'required|integer'
        ];
    }
}
