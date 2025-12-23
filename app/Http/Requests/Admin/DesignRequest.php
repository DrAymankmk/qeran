<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

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
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'en.name' => ['nullable', 'string', 'max:255'],
            'ar.name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
