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
            'actions' => 'array',
            'actions.*.model_type' => 'required|string',

            /*
             * data that determines what actions a role has on ALL objects that pertain to the model_type
             */
            'actions.*.class' => 'array',
            'actions.*.class.*' => 'array',
            'actions.*.class.*.actions' => 'array',
            'actions.*.class.*.actions.*' => 'string|in:create,delete,forceDelete,restore,update,view,viewAny',

            /*
             * data that determines what actions a role has on ONE object that pertains to model_type and model_id
             */
            'actions.*.object' => 'array',
            'actions.*.object.*' => 'array',
            'actions.*.object.*.model_id' => 'integer',
            'actions.*.object.*.actions' => 'array',
            'actions.*.object.*.actions.*' => 'string|in:create,delete,forceDelete,restore,update,view'

            // TODO: Refactor into a simpler rule without a callable
//            'actions.*.model_id' => [ 'int', Rule::requiredIf(function () {
//                $actions = \Request::get('actions');
//
//                return empty($actions) ? false
//                    : in_array('view', $actions);
//            })]
        ];
    }
}
