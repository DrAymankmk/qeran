<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromoCodeRequest extends FormRequest
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
        $promoCodeId = $this->route('promo_code') ?? $this->route('promo_code');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9_-]+$/i', // Alphanumeric, underscore, and dash only
                Rule::unique('promo_codes', 'code')->ignore($promoCodeId)
            ],
            'valid_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:expire_date'],
            'expire_date' => ['required', 'date', 'after_or_equal:today', 'after_or_equal:valid_date'],
            'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'is_active' => ['sometimes', 'boolean'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
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
            'code.required' => __('admin.code-required'),
            'code.unique' => __('admin.code-already-exists'),
            'code.regex' => __('admin.code-invalid-format'),
            'valid_date.required' => __('admin.valid-date-required'),
            'valid_date.after_or_equal' => __('admin.valid-date-must-be-today-or-later'),
            'valid_date.before_or_equal' => __('admin.valid-date-must-be-before-expire'),
            'expire_date.required' => __('admin.expire-date-required'),
            'expire_date.after_or_equal' => __('admin.expire-date-must-be-today-or-later'),
            'expire_date.after_or_equal.valid_date' => __('admin.expire-date-must-be-after-valid'),
            'discount_percentage.required' => __('admin.discount-percentage-required'),
            'discount_percentage.numeric' => __('admin.discount-percentage-must-be-numeric'),
            'discount_percentage.min' => __('admin.discount-percentage-min'),
            'discount_percentage.max' => __('admin.discount-percentage-max'),
            'package_id.exists' => __('admin.package-not-found'),
            'usage_limit.integer' => __('admin.usage-limit-must-be-integer'),
            'usage_limit.min' => __('admin.usage-limit-min'),
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
            'valid_date' => __('admin.valid-date'),
            'expire_date' => __('admin.expire-date'),
            'discount_percentage' => __('admin.discount-percentage'),
            'usage_limit' => __('admin.usage-limit'),
        ];
    }
}
