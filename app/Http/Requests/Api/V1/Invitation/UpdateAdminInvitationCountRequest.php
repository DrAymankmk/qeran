<?php

namespace App\Http\Requests\Api\V1\Invitation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminInvitationCountRequest extends FormRequest
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
        return [
            'invitation_count' => [
                'required',
                'integer',
                'min:1',
                'max:9999'
            ]
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'invitation_count.required' => __('validation.invitation_count_required'),
            'invitation_count.integer' => __('validation.invitation_count_integer'),
            'invitation_count.min' => __('validation.invitation_count_min'),
            'invitation_count.max' => __('validation.invitation_count_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'invitation_count' => __('validation.attributes.invitation_count'),
        ];
    }
}
