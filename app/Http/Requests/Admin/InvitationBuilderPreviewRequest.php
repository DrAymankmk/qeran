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
        $themeKeys = array_keys(config('invitation_builder.animated_themes', []));
        $envelopeKeys = array_keys(config('invitation_builder.envelope_colors', []));
        $sealKeys = array_keys(config('invitation_builder.seal_styles', []));
        $datePosKeys = array_keys(config('invitation_builder.date_positions', []));
        $blockKeys = array_keys(config('invitation_builder.information_blocks', []));

        return [
            'event_category' => ['nullable', 'string', Rule::in($eventKeys)],
            'theme_slug' => ['nullable', 'string', Rule::in($themeKeys)],
            'theme_mode' => ['nullable', 'string', Rule::in(['dark', 'light'])],
            'opening_type' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'background_color' => ['nullable', 'string', 'max:20'],
            'text_color' => ['nullable', 'string', 'max:20'],
            'font_family' => ['nullable', 'string', 'max:64'],
            'headline_font' => ['nullable', 'string', 'max:64'],
            'custom_css' => ['nullable', 'string', 'max:8000'],
            'welcome_title' => ['nullable', 'string', 'max:255'],
            'welcome_subtitle' => ['nullable', 'string', 'max:255'],
            'welcome_enabled' => ['nullable', 'boolean'],
            'music_enabled' => ['nullable', 'boolean'],
            'video_background' => ['nullable', 'boolean'],
            'intro_video_enabled' => ['nullable', 'boolean'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'background_media_url' => ['nullable', 'string', 'max:500'],
            'envelope_color' => ['nullable', 'string', Rule::in($envelopeKeys)],
            'seal_style' => ['nullable', 'string', Rule::in($sealKeys)],
            'envelope_initials' => ['nullable', 'string', 'max:8'],
            'opening_headline' => ['nullable', 'string', 'max:500'],
            'event_date' => ['nullable', 'string', 'max:32'],
            'event_time' => ['nullable', 'string', 'max:32'],
            'date_position' => ['nullable', 'string', Rule::in($datePosKeys)],
            'block_accent_color' => ['nullable', 'string', 'max:20'],
            'block_floral_border' => ['nullable', 'boolean'],
            'blocks' => ['nullable', 'array'],
            'blocks.*' => ['string', Rule::in($blockKeys)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $checkboxes = [
            'welcome_enabled',
            'music_enabled',
            'video_background',
            'intro_video_enabled',
            'block_floral_border',
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
