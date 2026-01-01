<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
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
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'en.name' => ['required', 'string', 'max:255'],
            'ar.name' => ['required', 'string', 'max:255'],
            'en.job' => ['nullable', 'string', 'max:255'],
            'ar.job' => ['nullable', 'string', 'max:255'],
            'en.message' => ['required', 'string'],
            'ar.message' => ['required', 'string'],
        ];
    }
}























