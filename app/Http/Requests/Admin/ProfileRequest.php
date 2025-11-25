<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $adminId = auth('admin')->id();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($adminId)
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
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
            'password.min' => __('admin.password-min'),
            'password.confirmed' => __('admin.password-confirmation-mismatch'),
            'image.image' => __('admin.image-must-be-image'),
            'image.mimes' => __('admin.image-invalid-format'),
            'image.max' => __('admin.image-max-size'),
        ];
    }
}
