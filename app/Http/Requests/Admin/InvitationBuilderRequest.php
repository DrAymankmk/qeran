<?php

namespace App\Http\Requests\Admin;

use App\Services\Invitation\InvitationBuilderService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvitationBuilderRequest extends FormRequest
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
            'event_category' => ['required', 'string', Rule::in($eventKeys)],
            'theme_slug' => ['required', 'string', Rule::in($themeKeys)],
            'theme_mode' => ['required', 'string', Rule::in(['dark', 'light'])],
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
            'logo_url' => ['nullable', 'url', 'max:500'],
            'background_media_url' => ['nullable', 'string', 'max:500'],
            'envelope_color' => ['required', 'string', Rule::in($envelopeKeys)],
            'envelope_shape' => ['nullable', 'string', Rule::in($envelopeShapeKeys)],
            'seal_style' => ['required', 'string', Rule::in($sealKeys)],
            'seal_color' => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'envelope_initials' => ['nullable', 'string', 'max:8'],
            'envelope_image_ref' => ['nullable', 'string', 'max:128'],
            'opening_headline' => ['nullable', 'string', 'max:500'],
            'groom' => ['nullable', 'string', 'max:50'],
            'bride' => ['nullable', 'string', 'max:50'],
            'groom_father' => ['nullable', 'string', 'max:50'],
            'bride_father' => ['nullable', 'string', 'max:50'],
            'event_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'string', 'max:32'],
            'date_position' => ['required', 'string', Rule::in($datePosKeys)],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_location' => ['nullable', 'string', 'max:500'],
            'ceremony_note' => ['nullable', 'string', 'max:255'],
            'reception_time' => ['nullable', 'string', 'max:32'],
            'reception_note' => ['nullable', 'string', 'max:255'],
            'details_section_title' => ['nullable', 'string', 'max:255'],
            'details_section_label' => ['nullable', 'string', 'max:255'],
            'block_accent_color' => ['nullable', 'string', 'max:20'],
            'block_floral_border' => ['nullable', 'boolean'],
            'blocks' => ['nullable', 'array'],
            'blocks.*' => ['string', Rule::in($blockKeys)],
            'publish' => ['nullable', 'boolean'],
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

        if (! $this->has('theme_mode') || $this->input('theme_mode') === '') {
            $merge['theme_mode'] = config('invitation_builder.defaults.theme_mode', 'dark');
        }

        if ($this->filled('theme_slug')) {
            $merge['theme_slug'] = app(InvitationBuilderService::class)->normalizeThemeSlug($this->input('theme_slug'));
        }

        $merge['envelope_shape'] = app(InvitationBuilderService::class)->normalizeEnvelopeShape($this->input('envelope_shape'));

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'theme_slug' => __('admin.ib-field-theme_slug'),
            'theme_mode' => __('admin.ib-field-theme_mode'),
            'event_category' => __('admin.ib-field-event_category'),
            'envelope_color' => __('admin.ib-field-envelope_color'),
            'envelope_shape' => __('admin.ib-field-envelope_shape'),
            'seal_style' => __('admin.ib-field-seal_style'),
            'seal_color' => __('admin.ib-field-seal_color'),
            'date_position' => __('admin.ib-field-date_position'),
            'background_media_url' => __('admin.ib-field-background_media_url'),
            'groom' => __('admin.groom'),
            'bride' => __('admin.bride'),
            'groom_father' => __('admin.groom_father'),
            'bride_father' => __('admin.bride_father'),
            'event_date' => __('admin.ib-event-date'),
            'event_time' => __('admin.ib-event-time'),
        ];
    }
}
