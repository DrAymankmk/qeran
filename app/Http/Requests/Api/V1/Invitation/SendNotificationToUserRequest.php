<?php

namespace App\Http\Requests\Api\V1\Invitation;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationToUserRequest extends FormRequest
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
            'message' => 'nullable|string|max:1000',
            'use_template' => 'boolean',
        ];
    }

    /**
     * Get the validation messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'message.string' => __('validation.message_string'),
            'message.max' => __('validation.message_max'),
            'use_template.boolean' => __('validation.use_template_boolean'),
        ];
    }
}