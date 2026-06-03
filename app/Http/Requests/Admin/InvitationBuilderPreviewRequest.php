<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvitationBuilderPreviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventKeys = array_keys(config('invitation_builder.event_types', []));
        $openingKeys = array_keys(config('invitation_builder.opening_types', []));

        return [
            'event_category' => ['nullable', 'string', Rule::in($eventKeys)],
            'theme_template' => ['nullable', 'integer', 'min:1', 'max:21'],
            'theme_mode' => ['nullable', 'string', Rule::in(['dark', 'light'])],
            'opening_type' => ['nullable', 'string', Rule::in($openingKeys)],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'background_color' => ['nullable', 'string', 'max:20'],
            'text_color' => ['nullable', 'string', 'max:20'],
            'font_family' => ['nullable', 'string', 'max:64'],
            'custom_css' => ['nullable', 'string', 'max:8000'],
            'welcome_title' => ['nullable', 'string', 'max:255'],
            'welcome_subtitle' => ['nullable', 'string', 'max:255'],
            'welcome_enabled' => ['nullable', 'boolean'],
            'music_enabled' => ['nullable', 'boolean'],
            'animated_theme' => ['nullable', 'boolean'],
            'video_background' => ['nullable', 'boolean'],
            'intro_video_enabled' => ['nullable', 'boolean'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'background_media_url' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $checkboxes = [
            'welcome_enabled',
            'music_enabled',
            'animated_theme',
            'video_background',
            'intro_video_enabled',
        ];

        $merge = [];
        foreach ($checkboxes as $field) {
            if (! $this->has($field)) {
                $merge[$field] = '0';
            }
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}
