<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
                'image' => ['nullable'],
                'ar.name'=>['required','string'],
                'en.name'=>['required','string'],
                'ar.title'=>['required','string'],
                'en.title'=>['required','string'],
                'ar.description'=>['required','string'],
                'en.description'=>['required','string'],

            ];

    }
    public function messages()
    {
        return[
            'ar.name.required'=>__('admin.name-required'),
            'ar.name.string'=>__('admin.string'),
        ];
    }
}
