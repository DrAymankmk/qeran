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
        $rules = [
            'package_invitation_type'=>['required'],
            'free_invitations_count'=>['required'],
            'package_type'=>['required'],
            'count'=>['required'],
            'price'=>['required'],
            'title'=>['nullable','string','max:255'],
            'subtitle'=>['nullable','string','max:500'],
            'content'=>['nullable','string'],
            'en.title'=>['nullable','string','max:255'],
            'en.subtitle'=>['nullable','string','max:500'],
            'en.content'=>['nullable','string'],
            'ar.title'=>['nullable','string','max:255'],
            'ar.subtitle'=>['nullable','string','max:500'],
            'ar.content'=>['nullable','string'],
        ];

        return $rules;
    }
    /**
     * Custom validation messages.
     * Keys must match Laravel’s "attribute.rule" format (same keys as in rules(), not ar.* prefixes).
     */
    public function messages()
    {
        return [
            'package_invitation_type.required' => __('messages.package-invitation-type-required'),
            'package_type.required' => __('messages.package-type-required'),
            'free_invitations_count.required' => __('messages.free-invitations-count-required'),
            'count.required' => __('messages.count-required'),
            'price.required' => __('messages.price-required'),
        ];
    }
}
