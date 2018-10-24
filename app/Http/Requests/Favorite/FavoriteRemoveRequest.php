<?php

namespace App\Http\Requests\Favorite;

use App\Favorite;
use Illuminate\Foundation\Http\FormRequest;

class FavoriteRemoveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'favorite_id' => 'required|integer|exists:favorites,id',
        ];
    }
}
