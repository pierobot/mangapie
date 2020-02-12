<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PutRoleRequest extends FormRequest
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
            'new-name' => 'string|unique:roles,name',
            'model_type' => 'required|string',
            'actions' => 'array',
            'actions.*' => 'string|in:create,delete,forceDelete,restore,update,view,viewAny',

            // TODO: Refactor into a simpler rule without a callable
            'model_id' => [ 'int', Rule::requiredIf(function () {
                $actions = \Request::get('actions');

                return empty($actions) ? false
                    : in_array('view', $actions);
            })]
        ];
    }
}
