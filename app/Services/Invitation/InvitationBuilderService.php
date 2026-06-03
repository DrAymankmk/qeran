<?php

namespace App\Services\Invitation;

use App\Helpers\Constant;
use App\Models\Invitation;
use App\Models\InvitationBuilderSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InvitationBuilderService
{
    public function catalog(): array
    {
        $themes = config('invitation_builder.animated_themes', []);

        return [
            'event_types' => config('invitation_builder.event_types', []),
            'theme_categories' => config('invitation_builder.theme_categories', []),
            'animated_themes' => $themes,
            'animated_themes_count' => count($themes),
            'envelope_colors' => config('invitation_builder.envelope_colors', []),
            'seal_styles' => config('invitation_builder.seal_styles', []),
            'fonts' => config('invitation_builder.fonts', []),
            'date_positions' => config('invitation_builder.date_positions', []),
            'information_blocks' => config('invitation_builder.information_blocks', []),
            'opening_types' => config('invitation_builder.opening_types', []),
            'theme_modes' => config('invitation_builder.theme_modes', []),
            'defaults' => config('invitation_builder.defaults', []),
        ];
    }

    public function themeDefinition(?string $slug): ?array
    {
        if (! $slug) {
            return null;
        }

        $themes = config('invitation_builder.animated_themes', []);

        return $themes[$slug] ?? null;
    }

    public function resolveTemplateFromThemeSlug(?string $slug): int
    {
        $theme = $this->themeDefinition($slug);
        $template = (int) ($theme['renderer_template'] ?? config('invitation_builder.defaults.theme_template', 16));

        return ($template >= 1 && $template <= 21) ? $template : 16;
    }

    public function resolve(Invitation $invitation, ?int $urlTemplateOverride = null): array
    {
        $defaults = config('invitation_builder.defaults', []);
        $row = $invitation->relationLoaded('builderSetting')
            ? $invitation->builderSetting
            : $invitation->builderSetting()->first();

        $json = is_array($row?->settings) ? $row->settings : [];

        $themeSlug = $json['theme_slug'] ?? $defaults['theme_slug'] ?? 'romantic-blush';
        $themeDef = $this->themeDefinition($themeSlug);

        $template = $urlTemplateOverride
            ?? (int) ($row?->theme_template ?? $this->resolveTemplateFromThemeSlug($themeSlug));

        if ($template < 1 || $template > 21) {
            $template = $this->resolveTemplateFromThemeSlug($themeSlug);
        }

        $blocks = $this->normalizeBlocks($json['blocks'] ?? $defaults['blocks'] ?? []);

        return array_merge($this->colorDefaults($themeDef, $json, $defaults), [
            'enabled' => $row !== null,
            'published' => $row?->isPublished() ?? false,
            'template' => $template,
            'theme_slug' => $themeSlug,
            'theme_name' => $themeDef['name_ar'] ?? $themeSlug,
            'event_category' => $row?->event_category ?? $defaults['event_category'] ?? 'wedding',
            'theme_mode' => $row?->theme_mode ?? $defaults['theme_mode'] ?? 'dark',
            'opening_type' => $row?->opening_type ?? $defaults['opening_type'] ?? 'envelope',
            'welcome_title' => $json['welcome_title'] ?? $invitation->event_name,
            'welcome_subtitle' => $json['welcome_subtitle'] ?? ($invitation->host_name ?? ''),
            'welcome_enabled' => (bool) ($json['welcome_enabled'] ?? ($row?->opening_type === 'welcome')),
            'music_enabled' => (bool) ($json['music_enabled'] ?? $defaults['music_enabled'] ?? true),
            'video_background' => (bool) ($json['video_background'] ?? false),
            'intro_video_enabled' => (bool) ($json['intro_video_enabled'] ?? ($row?->opening_type === 'intro_video')),
            'logo_url' => $json['logo_url'] ?? null,
            'background_media_url' => $json['background_media_url'] ?? null,
            'custom_css' => $json['custom_css'] ?? '',
            'envelope_color' => $json['envelope_color'] ?? $defaults['envelope_color'] ?? 'cream',
            'seal_style' => $json['seal_style'] ?? $defaults['seal_style'] ?? 'wax_classic',
            'envelope_initials' => $json['envelope_initials'] ?? $defaults['envelope_initials'] ?? '',
            'opening_headline' => $json['opening_headline'] ?? $invitation->event_name,
            'event_date' => $json['event_date'] ?? $invitation->date,
            'event_time' => $json['event_time'] ?? $invitation->time,
            'date_position' => $json['date_position'] ?? $defaults['date_position'] ?? 'center',
            'headline_font' => $json['headline_font'] ?? $defaults['headline_font'] ?? 'Playfair Display',
            'block_accent_color' => $json['block_accent_color'] ?? $defaults['block_accent_color'] ?? '#c9a962',
            'block_floral_border' => (bool) ($json['block_floral_border'] ?? $defaults['block_floral_border'] ?? true),
            'blocks' => $blocks,
        ]);
    }

    public function resolveFromDraft(Invitation $invitation, array $data): array
    {
        $base = $this->resolve($invitation);

        $themeSlug = $data['theme_slug'] ?? $base['theme_slug'];
        $themeDef = $this->themeDefinition($themeSlug);
        $template = $this->resolveTemplateFromThemeSlug($themeSlug);

        $bool = function (string $key) use ($data, $base): bool {
            if (! array_key_exists($key, $data)) {
                return (bool) $base[$key];
            }

            return filter_var($data[$key], FILTER_VALIDATE_BOOLEAN);
        };

        $welcomeEnabled = $bool('welcome_enabled');
        $introEnabled = $bool('intro_video_enabled');
        $openingType = $data['opening_type'] ?? $base['opening_type'];
        if ($welcomeEnabled) {
            $openingType = 'welcome';
        }
        if ($introEnabled) {
            $openingType = 'intro_video';
        }

        $colors = $this->colorDefaults($themeDef, $data, []);
        if (! empty($data['primary_color'])) {
            $colors['primary_color'] = $data['primary_color'];
        }
        if (! empty($data['secondary_color'])) {
            $colors['secondary_color'] = $data['secondary_color'];
        }
        if (! empty($data['background_color'])) {
            $colors['background_color'] = $data['background_color'];
        }
        if (! empty($data['text_color'])) {
            $colors['text_color'] = $data['text_color'];
        }

        $blocks = isset($data['blocks'])
            ? $this->normalizeBlocks($data['blocks'])
            : $base['blocks'];

        return array_merge($colors, [
            'enabled' => true,
            'published' => true,
            'template' => $template,
            'theme_slug' => $themeSlug,
            'theme_name' => $themeDef['name_ar'] ?? $themeSlug,
            'event_category' => $data['event_category'] ?? $base['event_category'],
            'theme_mode' => $data['theme_mode'] ?? $base['theme_mode'],
            'opening_type' => $openingType,
            'font_family' => $data['font_family'] ?? $base['font_family'],
            'headline_font' => $data['headline_font'] ?? $base['headline_font'],
            'custom_css' => array_key_exists('custom_css', $data) ? (string) $data['custom_css'] : $base['custom_css'],
            'welcome_title' => $data['welcome_title'] ?? $base['welcome_title'],
            'welcome_subtitle' => $data['welcome_subtitle'] ?? $base['welcome_subtitle'],
            'welcome_enabled' => $welcomeEnabled,
            'music_enabled' => $bool('music_enabled'),
            'video_background' => $bool('video_background'),
            'intro_video_enabled' => $introEnabled,
            'logo_url' => $data['logo_url'] ?? $base['logo_url'],
            'background_media_url' => $data['background_media_url'] ?? $base['background_media_url'],
            'envelope_color' => $data['envelope_color'] ?? $base['envelope_color'],
            'seal_style' => $data['seal_style'] ?? $base['seal_style'],
            'envelope_initials' => $data['envelope_initials'] ?? $base['envelope_initials'],
            'opening_headline' => $data['opening_headline'] ?? $base['opening_headline'],
            'event_date' => $data['event_date'] ?? $base['event_date'],
            'event_time' => $data['event_time'] ?? $base['event_time'],
            'date_position' => $data['date_position'] ?? $base['date_position'],
            'block_accent_color' => $data['block_accent_color'] ?? $base['block_accent_color'],
            'block_floral_border' => $bool('block_floral_border'),
            'blocks' => $blocks,
        ]);
    }

    public function upsert(Invitation $invitation, array $data): InvitationBuilderSetting
    {
        $themeSlug = $data['theme_slug'] ?? config('invitation_builder.defaults.theme_slug', 'romantic-blush');
        $template = $this->resolveTemplateFromThemeSlug($themeSlug);

        $settings = [
            'theme_slug' => $themeSlug,
            'primary_color' => $data['primary_color'] ?? null,
            'secondary_color' => $data['secondary_color'] ?? null,
            'background_color' => $data['background_color'] ?? null,
            'text_color' => $data['text_color'] ?? null,
            'font_family' => $data['font_family'] ?? null,
            'headline_font' => $data['headline_font'] ?? null,
            'custom_css' => $data['custom_css'] ?? null,
            'welcome_title' => $data['welcome_title'] ?? null,
            'welcome_subtitle' => $data['welcome_subtitle'] ?? null,
            'welcome_enabled' => filter_var($data['welcome_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'music_enabled' => filter_var($data['music_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'video_background' => filter_var($data['video_background'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'intro_video_enabled' => filter_var($data['intro_video_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'logo_url' => $data['logo_url'] ?? null,
            'background_media_url' => $data['background_media_url'] ?? null,
            'envelope_color' => $data['envelope_color'] ?? null,
            'seal_style' => $data['seal_style'] ?? null,
            'envelope_initials' => $data['envelope_initials'] ?? null,
            'opening_headline' => $data['opening_headline'] ?? null,
            'event_date' => $data['event_date'] ?? null,
            'event_time' => $data['event_time'] ?? null,
            'date_position' => $data['date_position'] ?? null,
            'block_accent_color' => $data['block_accent_color'] ?? null,
            'block_floral_border' => filter_var($data['block_floral_border'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'blocks' => isset($data['blocks']) ? $this->normalizeBlocks($data['blocks']) : null,
        ];

        $openingType = $data['opening_type'] ?? 'envelope';
        if ($settings['welcome_enabled']) {
            $openingType = 'welcome';
        }
        if ($settings['intro_video_enabled']) {
            $openingType = 'intro_video';
        }

        $existing = $invitation->builderSetting;
        $publishedAt = null;
        if (! empty($data['publish'])) {
            $publishedAt = $existing?->published_at ?? now();
        }

        $filtered = array_filter(
            $settings,
            fn ($v, $k) => $k === 'blocks'
                ? is_array($v)
                : ($v !== null && $v !== '' && $v !== []),
            ARRAY_FILTER_USE_BOTH
        );

        return InvitationBuilderSetting::query()->updateOrCreate(
            ['invitation_id' => $invitation->id],
            [
                'event_category' => $data['event_category'] ?? 'wedding',
                'theme_template' => $template,
                'theme_mode' => $data['theme_mode'] ?? 'dark',
                'opening_type' => $openingType,
                'settings' => $filtered,
                'published_at' => $publishedAt,
            ]
        );
    }

    public function normalizeBlocks(mixed $blocks): array
    {
        $catalog = array_keys(config('invitation_builder.information_blocks', []));
        $list = is_array($blocks) ? $blocks : [];
        $normalized = [];

        foreach ($list as $item) {
            $slug = is_string($item) ? $item : ($item['type'] ?? null);
            if ($slug && in_array($slug, $catalog, true) && ! in_array($slug, $normalized, true)) {
                $normalized[] = $slug;
            }
        }

        return $normalized !== [] ? $normalized : (config('invitation_builder.defaults.blocks') ?? ['countdown', 'venue', 'rsvp']);
    }

    protected function colorDefaults(?array $themeDef, array $json, array $defaults): array
    {
        return [
            'primary_color' => $json['primary_color'] ?? $themeDef['primary_color'] ?? $defaults['primary_color'] ?? '#c9a962',
            'secondary_color' => $json['secondary_color'] ?? $themeDef['secondary_color'] ?? $defaults['secondary_color'] ?? '#e8b4b8',
            'background_color' => $json['background_color'] ?? $themeDef['background_color'] ?? $defaults['background_color'] ?? '#1a1520',
            'text_color' => $json['text_color'] ?? $themeDef['text_color'] ?? $defaults['text_color'] ?? '#faf6f0',
            'font_family' => $json['font_family'] ?? $defaults['font_family'] ?? 'Cairo',
        ];
    }

    public function resolvePreviewGuestIds(Invitation $invitation): array
    {
        $invitation->loadMissing('users');
        $guest = $invitation->users()->orderBy('invitation_user.id')->first();

        if ($guest) {
            return [
                'user_id' => (int) $guest->id,
                'inserted_by' => (int) ($guest->pivot->invited_by ?? $invitation->user_id),
            ];
        }

        return [
            'user_id' => (int) $invitation->user_id,
            'inserted_by' => (int) $invitation->user_id,
        ];
    }

    public function resolveGuestForShow(Invitation $invitation, int $userId, ?int $insertedBy, bool $builderPreview): ?User
    {
        if ($insertedBy !== null) {
            $guest = $invitation->users()
                ->where('invitation_user.user_id', $userId)
                ->where('invitation_user.invited_by', $insertedBy)
                ->first();

            if ($guest) {
                return $guest;
            }
        }

        $guest = $invitation->users()
            ->where('invitation_user.user_id', $userId)
            ->first();

        if ($guest) {
            return $guest;
        }

        if (! $builderPreview) {
            return null;
        }

        $anyGuest = $invitation->users()->orderBy('invitation_user.id')->first();
        if ($anyGuest) {
            return $anyGuest;
        }

        return $this->syntheticPreviewUser($invitation);
    }

    protected function syntheticPreviewUser(Invitation $invitation): User
    {
        $user = User::query()->find($invitation->user_id) ?? new User([
            'name' => __('admin.invitation-builder-preview-guest'),
        ]);

        if (! $user->id) {
            $user->id = $invitation->user_id;
        }

        $user->setRelation('pivot', new Pivot([
            'name' => __('admin.invitation-builder-preview-guest'),
            'invitation_count' => 1,
            'seen' => Constant::SEEN_STATUS['in app'],
            'invited_by' => $invitation->user_id,
        ], 'invitation_user'));

        return $user;
    }

    public function previewUrl(Invitation $invitation): string
    {
        $invitation->loadMissing(['users', 'builderSetting']);
        $json = is_array($invitation->builderSetting?->settings)
            ? $invitation->builderSetting->settings
            : [];
        $template = (int) ($invitation->builderSetting?->theme_template
            ?? $this->resolveTemplateFromThemeSlug($json['theme_slug'] ?? null));
        $ids = $this->resolvePreviewGuestIds($invitation);

        $url = route('user.invitation.show', [
            'invitation_code' => $invitation->code,
            'user_id' => $ids['user_id'],
            'inserted_by' => $ids['inserted_by'],
            'template' => $template,
        ]);

        return $url.(str_contains($url, '?') ? '&' : '?').'builder=1';
    }
}
