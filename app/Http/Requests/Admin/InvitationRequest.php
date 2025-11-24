<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
            return [
                'file' => ['nullable'],
            ];

    }
    public function messages()
    {
        return[
            'ar.name.required'=>__('admin.name-required'),
            'ar.name.string'=>__('admin.string'),
        ];
    }
}
