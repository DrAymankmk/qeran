<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Constant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class DesignRequest extends FormRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['nullable', 'string', 'max:255'],
            'show_on' => ['nullable', 'array'],
            'show_on.*' => ['nullable', 'string', 'in:home,footer,gallery,services,about'],
            'image' => [
                'nullable',
                File::types(['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg', 'ogv', 'mov', 'm4v'])
                    ->max(Constant::DESIGN_MEDIA_MAX_UPLOAD_KB),
            ],
            'en.name' => ['nullable', 'string', 'max:255'],
            'ar.name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => __('validation.attributes.design_category_id'),
            'code' => __('validation.attributes.design_code'),
            'image' => __('validation.attributes.design_media'),
            'show_on' => __('validation.attributes.design_show_on'),
            'show_on.*' => __('validation.attributes.design_show_on_page'),
            'en.name' => __('validation.attributes.design_name_en'),
            'ar.name' => __('validation.attributes.design_name_ar'),
        ];
    }

    public function messages(): array
    {
        $maxMb = (int) round(Constant::DESIGN_MEDIA_MAX_UPLOAD_KB / 1024);

        return [
            'image.max' => __('validation.design_media_max', ['max' => $maxMb]),
            'image.mimes' => __('validation.design_media_mimes'),
            'image.mimetypes' => __('validation.design_media_mimes'),
        ];
    }
}
