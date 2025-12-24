<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NotificationsRequest extends FormRequest
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
        $routeName = $this->route()->getName();
        
        // Handle both create format (ar[title]) and edit format (title[ar])
        if (str_contains($routeName, 'update') || str_contains($routeName, 'edit')) {
            // Edit form format: title[ar], title[en], description[ar], description[en]
            return [
                'title.ar' => ['required', 'string'],
                'title.en' => ['required', 'string'],
                'description.ar' => ['required', 'string'],
                'description.en' => ['required', 'string'],
            ];
        } else {
            // Create form format: ar[title], en[title], ar[description], en[description]
            return [
                'ar.title' => ['required', 'string'],
                'en.title' => ['required', 'string'],
                'ar.description' => ['required', 'string'],
                'en.description' => ['required', 'string'],
            ];
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        $routeName = $this->route()->getName();
        
        if (str_contains($routeName, 'update') || str_contains($routeName, 'edit')) {
            return [
                'title.ar.required' => __('admin.arabic-title-required'),
                'title.ar.string' => __('admin.arabic-title-must-be-string'),
                'title.en.required' => __('admin.english-title-required'),
                'title.en.string' => __('admin.english-title-must-be-string'),
                'description.ar.required' => __('admin.arabic-description-required'),
                'description.ar.string' => __('admin.arabic-description-must-be-string'),
                'description.en.required' => __('admin.english-description-required'),
                'description.en.string' => __('admin.english-description-must-be-string'),
            ];
        } else {
            return [
                'ar.title.required' => __('admin.arabic-title-required'),
                'ar.title.string' => __('admin.arabic-title-must-be-string'),
                'en.title.required' => __('admin.english-title-required'),
                'en.title.string' => __('admin.english-title-must-be-string'),
                'ar.description.required' => __('admin.arabic-description-required'),
                'ar.description.string' => __('admin.arabic-description-must-be-string'),
                'en.description.required' => __('admin.english-description-required'),
                'en.description.string' => __('admin.english-description-must-be-string'),
            ];
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        $routeName = $this->route()->getName();
        
        if (str_contains($routeName, 'update') || str_contains($routeName, 'edit')) {
            return [
                'title.ar' => __('admin.arabic-title'),
                'title.en' => __('admin.english-title'),
                'description.ar' => __('admin.arabic-description'),
                'description.en' => __('admin.english-description'),
            ];
        } else {
            return [
                'ar.title' => __('admin.arabic-title'),
                'en.title' => __('admin.english-title'),
                'ar.description' => __('admin.arabic-description'),
                'en.description' => __('admin.english-description'),
            ];
        }
    }
}
