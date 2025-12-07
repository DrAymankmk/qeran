<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
        $adminId = $this->route('admin') ?? $this->route('id');

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:160', 'unique:admins,email,' . $adminId],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
                'min:6',
                'max:255'
            ],
            'active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'mimes:jpeg,jpg,png,gif', 'max:20000'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => __('admin.name-required'),
            'email.required' => __('admin.email-required'),
            'email.email' => __('admin.email-invalid'),
            'email.unique' => __('admin.email-already-exists'),
            'password.required' => __('admin.password-required'),
            'password.min' => __('admin.password-min'),
            'image.mimes' => __('admin.image-mimes'),
            'image.max' => __('admin.image-max'),
            'roles.required' => __('admin.roles-required'),
            'roles.min' => __('admin.admin-must-have-at-least-one-role'),
            'roles.*.exists' => __('admin.invalid-role-selected'),
        ];
    }
}





