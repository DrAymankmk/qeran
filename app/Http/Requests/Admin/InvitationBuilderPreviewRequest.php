<?php

namespace App\Http\Requests\Admin;

use App\Services\Invitation\InvitationBuilderService;
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
        $themeKeys = app(InvitationBuilderService::class)->themeSlugs();
        $envelopeKeys = array_keys(config('invitation_builder.envelope_colors', []));
        $envelopeShapeKeys = array_keys(config('invitation_builder.envelope_shapes', []));
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
            'custom_css' => ['nullable', 'string'],
            'welcome_title' => ['nullable', 'string'],
            'welcome_subtitle' => ['nullable', 'string'],
            'welcome_enabled' => ['nullable', 'boolean'],
            'music_enabled' => ['nullable', 'boolean'],
            'video_background' => ['nullable', 'boolean'],
            'intro_video_enabled' => ['nullable', 'boolean'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'background_media_url' => ['nullable', 'string', 'max:500'],
            'envelope_color' => ['nullable', 'string', Rule::in($envelopeKeys)],
            'envelope_shape' => ['nullable', 'string', Rule::in($envelopeShapeKeys)],
            'seal_style' => ['nullable', 'string', Rule::in($sealKeys)],
            'seal_color' => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'envelope_initials' => ['nullable', 'string'],
            'envelope_image_ref' => ['nullable', 'string', 'max:128'],
            'opening_headline' => ['nullable', 'string'],
            'groom' => ['nullable', 'string'],
            'bride' => ['nullable', 'string'],
            'groom_father' => ['nullable', 'string'],
            'bride_father' => ['nullable', 'string'],
            'event_date' => ['nullable', 'string', 'max:32'],
            'event_time' => ['nullable', 'string', 'max:32'],
            'date_position' => ['nullable', 'string', Rule::in($datePosKeys)],
            'venue_name' => ['nullable', 'string'],
            'venue_location' => ['nullable', 'string'],
            'ceremony_note' => ['nullable', 'string'],
            'reception_time' => ['nullable', 'string', 'max:32'],
            'reception_note' => ['nullable', 'string'],
            'details_section_title' => ['nullable', 'string'],
            'details_section_label' => ['nullable', 'string'],
            'block_accent_color' => ['nullable', 'string', 'max:20'],
            'block_floral_border' => ['nullable', 'boolean'],
            'hero_enabled' => ['nullable', 'boolean'],
            'blocks' => ['nullable', 'array'],
            'blocks.*' => ['string', Rule::in($blockKeys)],
            'block_data' => ['nullable', 'array'],
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
            'hero_enabled',
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

        if ($this->filled('theme_slug')) {
            $this->merge([
                'theme_slug' => app(InvitationBuilderService::class)->normalizeThemeSlug($this->input('theme_slug')),
            ]);
        }

        $blockData = is_array($this->input('block_data')) ? $this->input('block_data') : [];
        if ($this->filled('details_section_label')) {
            $blockData['event_details']['label'] = $this->input('details_section_label');
        }
        if ($this->filled('details_section_title')) {
            $blockData['event_details']['title'] = $this->input('details_section_title');
        }
        if ($this->has('block_data') || $this->has('blocks') || $blockData !== []) {
            $this->merge([
                'block_data' => app(InvitationBuilderService::class)->normalizeBlockData(
                    $blockData,
                    $this->route('invitation')
                ),
            ]);
        }
    }
}
