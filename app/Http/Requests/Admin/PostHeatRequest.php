<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PostHeatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Auth::user()->hasRole('Administrator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action' => 'required|in:reset,set',
            'heat_default' => 'required_if:action,set|numeric',
            'heat_threshold' => 'required_if:action,set|numeric|max:' . (int) request()->get('heat_default'),
            'heat_heat' => 'required_if:action,set|numeric',
            'heat_cooldown' => 'required_if:action,set|numeric'
        ];
    }
}
