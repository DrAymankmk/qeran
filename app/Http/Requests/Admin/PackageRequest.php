<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
                'package_invitation_type'=>['required'],
                'free_invitations_count'=>['required'],
                'package_type'=>['required'],
                'count'=>['required'],
                'price'=>['required']
            ];

    }
    public function messages()
    {
        return[
            'ar.package_invitation_type.required'=>__('admin.package_invitation_type-required'),
            'ar.name.string'=>__('admin.string'),
        ];
    }
}
