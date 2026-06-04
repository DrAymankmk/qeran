<?php

namespace App\Services\Invitation;

use App\Helpers\Constant;
use App\Models\HubFile;
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
            'envelope_shapes' => config('invitation_builder.envelope_shapes', []),
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

        if (isset($themes[$slug])) {
            return $themes[$slug];
        }

        $alias = config('invitation_builder.theme_slug_aliases.'.$slug);

        return $alias ? ($themes[$alias] ?? null) : null;
    }

    public function normalizeEnvelopeShape(?string $shape): string
    {
        $shape = trim((string) ($shape ?? ''));
        $shapes = config('invitation_builder.envelope_shapes', []);

        if ($shape !== '' && isset($shapes[$shape])) {
            return $shape;
        }

        return config('invitation_builder.defaults.envelope_shape', 'classic');
    }

    public function normalizeThemeSlug(?string $slug): string
    {
        $slug = trim((string) ($slug ?? ''));
        $themes = config('invitation_builder.animated_themes', []);

        if ($slug !== '' && isset($themes[$slug])) {
            return $slug;
        }

        $alias = config('invitation_builder.theme_slug_aliases.'.$slug);

        if ($alias && isset($themes[$alias])) {
            return $alias;
        }

        return config('invitation_builder.defaults.theme_slug', 'opening-gold-bloom');
    }

    protected function themeOpeningVideoUrl(?array $themeDef): ?string
    {
        $url = trim((string) ($themeDef['opening_video_url'] ?? ''));

        return $url !== '' ? $url : null;
    }

    /**
     * @return array{background_media_url: ?string, video_background: bool, opening_video_url: ?string}
     */
    protected function resolveThemeMedia(?array $themeDef, array $json, bool $videoFlag): array
    {
        $opening = $this->themeOpeningVideoUrl($themeDef);
        $custom = trim((string) ($json['background_media_url'] ?? ''));

        if ($opening !== null && ($custom === '' || $custom === $opening)) {
            return [
                'background_media_url' => $opening,
                'video_background' => true,
                'opening_video_url' => $opening,
            ];
        }

        if ($custom !== '' && ($videoFlag || $opening !== null)) {
            return [
                'background_media_url' => $custom,
                'video_background' => $videoFlag || $custom === $opening,
                'opening_video_url' => $opening,
            ];
        }

        return [
            'background_media_url' => $custom !== '' ? $custom : null,
            'video_background' => $videoFlag,
            'opening_video_url' => $opening,
        ];
    }

    public function resolveRenderer(?string $slug): string
    {
        $theme = $this->themeDefinition($slug);

        return $theme['renderer'] ?? config('invitation_builder.defaults.renderer', 'builder-wedding');
    }

    public function resolveViewName(?string $slug): string
    {
        $renderer = $this->resolveRenderer($slug);
        $view = 'invitation.templates.'.$renderer;

        return view()->exists($view) ? $view : 'invitation.templates.builder-wedding';
    }

    /** @deprecated Legacy numeric templates; builder uses renderer views. */
    public function resolveTemplateFromThemeSlug(?string $slug): int
    {
        $theme = $this->themeDefinition($slug);
        if (! empty($theme['renderer'])) {
            return 0;
        }

        $template = (int) ($theme['renderer_template'] ?? 0);

        return ($template >= 1 && $template <= 21) ? $template : 0;
    }

    public function resolve(Invitation $invitation, ?int $urlTemplateOverride = null): array
    {
        $defaults = config('invitation_builder.defaults', []);
        $row = $invitation->relationLoaded('builderSetting')
            ? $invitation->builderSetting
            : $invitation->builderSetting()->first();

        $json = is_array($row?->settings) ? $row->settings : [];

        $themeSlug = $this->normalizeThemeSlug($json['theme_slug'] ?? $defaults['theme_slug'] ?? null);
        $themeDef = $this->themeDefinition($themeSlug);
        $themeMedia = $this->resolveThemeMedia(
            $themeDef,
            $json,
            (bool) ($json['video_background'] ?? false)
        );

        $renderer = $this->resolveRenderer($themeSlug);
        $viewName = $this->resolveViewName($themeSlug);
        $template = (int) ($row?->theme_template ?? 0);
        if ($renderer === 'builder-wedding') {
            $template = 0;
        } elseif ($template < 1 || $template > 21) {
            $template = $this->resolveTemplateFromThemeSlug($themeSlug) ?: 1;
        }

        $blocks = $this->normalizeBlocks($json['blocks'] ?? $defaults['blocks'] ?? []);

        return array_merge($this->colorDefaults($themeDef, $json, $defaults), [
            'enabled' => $row !== null,
            'published' => $row?->isPublished() ?? false,
            'template' => $template,
            'renderer' => $renderer,
            'view' => $viewName,
            'theme_slug' => $themeSlug,
            'theme_name' => $themeDef['name_ar'] ?? $themeSlug,
            'event_category' => $row?->event_category ?? $defaults['event_category'] ?? 'wedding',
            'theme_mode' => $row?->theme_mode ?? $defaults['theme_mode'] ?? 'dark',
            'opening_type' => $row?->opening_type ?? $defaults['opening_type'] ?? 'envelope',
            'welcome_title' => $json['welcome_title'] ?? $invitation->event_name,
            'welcome_subtitle' => $json['welcome_subtitle'] ?? ($invitation->host_name ?? ''),
            'welcome_enabled' => (bool) ($json['welcome_enabled'] ?? ($row?->opening_type === 'welcome')),
            'music_enabled' => (bool) ($json['music_enabled'] ?? $defaults['music_enabled'] ?? true),
            'video_background' => $themeMedia['video_background'],
            'intro_video_enabled' => (bool) ($json['intro_video_enabled'] ?? ($row?->opening_type === 'intro_video')),
            'logo_url' => $json['logo_url'] ?? null,
            'background_media_url' => $themeMedia['background_media_url'],
            'opening_video_url' => $themeMedia['opening_video_url'],
            'custom_css' => $json['custom_css'] ?? '',
            'envelope_color' => $json['envelope_color'] ?? $defaults['envelope_color'] ?? 'cream',
            'envelope_shape' => $this->normalizeEnvelopeShape($json['envelope_shape'] ?? $defaults['envelope_shape'] ?? null),
            'seal_style' => $json['seal_style'] ?? $defaults['seal_style'] ?? 'wax_classic',
            'seal_color' => WeddingInvitationPresenter::resolveSealColor(
                $json['seal_style'] ?? $defaults['seal_style'] ?? 'wax_classic',
                $json['seal_color'] ?? $defaults['seal_color'] ?? null
            ),
            'envelope_initials' => $json['envelope_initials'] ?? $defaults['envelope_initials'] ?? '',
            'envelope_image_url' => $json['envelope_image_url'] ?? $defaults['envelope_image_url'] ?? '',
            'envelope_image_ref' => $json['envelope_image_ref'] ?? $defaults['envelope_image_ref'] ?? '',
            'opening_headline' => $json['opening_headline'] ?? $invitation->event_name,
            'event_date' => $json['event_date'] ?? $invitation->date,
            'event_time' => $json['event_time'] ?? $invitation->time,
            'date_position' => $json['date_position'] ?? $defaults['date_position'] ?? 'center',
            'headline_font' => $json['headline_font'] ?? $defaults['headline_font'] ?? 'Playfair Display',
            'block_accent_color' => $json['block_accent_color'] ?? $defaults['block_accent_color'] ?? '#c9a962',
            'block_floral_border' => (bool) ($json['block_floral_border'] ?? $defaults['block_floral_border'] ?? true),
            'blocks' => $blocks,
            'venue_name' => $json['venue_name'] ?? $invitation->event_name ?? '',
            'venue_location' => $json['venue_location'] ?? $invitation->address ?? '',
            'ceremony_note' => $json['ceremony_note'] ?? '',
            'reception_time' => $json['reception_time'] ?? '',
            'reception_note' => $json['reception_note'] ?? '',
            'details_section_title' => $json['details_section_title'] ?? $invitation->event_name ?? '',
            'details_section_label' => $json['details_section_label'] ?? $defaults['details_section_label'] ?? 'جميع التفاصيل',
        ]);
    }

    public function resolveFromDraft(Invitation $invitation, array $data): array
    {
        $base = $this->resolve($invitation);

        $themeSlug = $this->normalizeThemeSlug($data['theme_slug'] ?? $base['theme_slug']);
        $themeDef = $this->themeDefinition($themeSlug);
        $renderer = $this->resolveRenderer($themeSlug);
        $viewName = $this->resolveViewName($themeSlug);
        $template = $renderer === 'builder-wedding' ? 0 : $this->resolveTemplateFromThemeSlug($themeSlug);

        $bool = function (string $key) use ($data, $base): bool {
            if (! array_key_exists($key, $data)) {
                return (bool) $base[$key];
            }

            return filter_var($data[$key], FILTER_VALIDATE_BOOLEAN);
        };

        $welcomeEnabled = $bool('welcome_enabled');
        $introEnabled = $bool('intro_video_enabled');
        $openingType = ! empty($data['opening_type'])
            ? (string) $data['opening_type']
            : ($base['opening_type'] ?? 'envelope');
        if ($introEnabled) {
            $openingType = 'intro_video';
        } elseif ($welcomeEnabled && $openingType !== 'envelope') {
            $openingType = 'welcome';
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
            'renderer' => $renderer,
            'view' => $viewName,
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
            ...$this->resolveThemeMedia(
                $themeDef,
                [
                    'background_media_url' => $data['background_media_url'] ?? $base['background_media_url'],
                    'video_background' => array_key_exists('video_background', $data)
                        ? $data['video_background']
                        : $base['video_background'],
                ],
                $bool('video_background')
            ),
            'intro_video_enabled' => $introEnabled,
            'logo_url' => $data['logo_url'] ?? $base['logo_url'],
            'envelope_color' => $data['envelope_color'] ?? $base['envelope_color'],
            'envelope_shape' => $this->normalizeEnvelopeShape($data['envelope_shape'] ?? $base['envelope_shape']),
            'seal_style' => $data['seal_style'] ?? $base['seal_style'],
            'seal_color' => WeddingInvitationPresenter::resolveSealColor(
                $data['seal_style'] ?? $base['seal_style'],
                $data['seal_color'] ?? $base['seal_color'] ?? null
            ),
            'envelope_initials' => $this->draftString($data, $base, 'envelope_initials'),
            'envelope_image_ref' => $this->draftString($data, $base, 'envelope_image_ref'),
            'opening_headline' => $this->draftString($data, $base, 'opening_headline'),
            'event_date' => $this->draftString($data, $base, 'event_date'),
            'event_time' => $this->draftString($data, $base, 'event_time'),
            'date_position' => $data['date_position'] ?? $base['date_position'],
            'block_accent_color' => $data['block_accent_color'] ?? $base['block_accent_color'],
            'block_floral_border' => $bool('block_floral_border'),
            'blocks' => $blocks,
            'venue_name' => $this->draftString($data, $base, 'venue_name'),
            'venue_location' => $this->draftString($data, $base, 'venue_location'),
            'ceremony_note' => $this->draftString($data, $base, 'ceremony_note'),
            'reception_time' => $this->draftString($data, $base, 'reception_time'),
            'reception_note' => $this->draftString($data, $base, 'reception_note'),
            'details_section_title' => $this->draftString($data, $base, 'details_section_title'),
            'details_section_label' => $this->draftString($data, $base, 'details_section_label'),
        ]);
    }

    public function upsert(Invitation $invitation, array $data): InvitationBuilderSetting
    {
        $themeSlug = $this->normalizeThemeSlug($data['theme_slug'] ?? null);
        $renderer = $this->resolveRenderer($themeSlug);
        $template = $renderer === 'builder-wedding' ? 0 : $this->resolveTemplateFromThemeSlug($themeSlug);

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
            'envelope_shape' => $this->normalizeEnvelopeShape($data['envelope_shape'] ?? null),
            'seal_style' => $data['seal_style'] ?? null,
            'seal_color' => WeddingInvitationPresenter::normalizeSealHex($data['seal_color'] ?? null)
                ?? WeddingInvitationPresenter::defaultSealColorForStyle($data['seal_style'] ?? null),
            'envelope_initials' => $data['envelope_initials'] ?? null,
            'envelope_image_ref' => $data['envelope_image_ref'] ?? null,
            'opening_headline' => $data['opening_headline'] ?? null,
            'event_date' => $data['event_date'] ?? null,
            'event_time' => $data['event_time'] ?? null,
            'date_position' => $data['date_position'] ?? null,
            'block_accent_color' => $data['block_accent_color'] ?? null,
            'block_floral_border' => filter_var($data['block_floral_border'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'blocks' => isset($data['blocks']) ? $this->normalizeBlocks($data['blocks']) : null,
            'venue_name' => $data['venue_name'] ?? null,
            'venue_location' => $data['venue_location'] ?? null,
            'ceremony_note' => $data['ceremony_note'] ?? null,
            'reception_time' => $data['reception_time'] ?? null,
            'reception_note' => $data['reception_note'] ?? null,
            'details_section_title' => $data['details_section_title'] ?? null,
            'details_section_label' => $data['details_section_label'] ?? null,
        ];

        $openingType = ! empty($data['opening_type'])
            ? (string) $data['opening_type']
            : 'envelope';
        if ($settings['intro_video_enabled']) {
            $openingType = 'intro_video';
        } elseif ($settings['welcome_enabled'] && $openingType !== 'envelope') {
            $openingType = 'welcome';
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

    /**
     * @return list<array{id: string, url: string, label: string, group: string}>
     */
    public function envelopeImageChoices(Invitation $invitation): array
    {
        $choices = [];
        $seenIds = [];
        $seenUrls = [];

        $add = function (string $id, string $url, string $label, string $group) use (&$choices, &$seenIds, &$seenUrls): void {
            if ($url === '' || isset($seenIds[$id]) || isset($seenUrls[$url])) {
                return;
            }
            $seenIds[$id] = true;
            $seenUrls[$url] = true;
            $choices[] = [
                'id' => $id,
                'url' => $url,
                'label' => $label,
                'group' => $group,
            ];
        };

        foreach (config('invitation_builder.envelope_images', []) as $key => $meta) {
            $path = (string) ($meta['path'] ?? '');
            if ($path === '') {
                continue;
            }
            $add(
                'stock:'.$key,
                asset($path),
                (string) ($meta['label_ar'] ?? $key),
                'stock'
            );
        }

        $dir = public_path('images/invitation-builder/envelopes');
        if (is_dir($dir)) {
            $files = glob($dir.'/*.{jpg,jpeg,png,webp,gif,svg}', GLOB_BRACE) ?: [];
            foreach ($files as $file) {
                $basename = basename($file);
                $add(
                    'stock:'.$basename,
                    asset('images/invitation-builder/envelopes/'.$basename),
                    pathinfo($basename, PATHINFO_FILENAME),
                    'stock'
                );
            }
        }

        $invitation->loadMissing('hubFiles');
        foreach ($invitation->hubFiles as $file) {
            if (! $file instanceof HubFile || (int) $file->file_type !== Constant::FILE_TYPE['Image']) {
                continue;
            }
            try {
                $url = (string) ($file->get_path() ?? '');
            } catch (\Throwable) {
                continue;
            }
            $label = trim((string) ($file->original_name ?? ''));
            if ($label === '') {
                $label = __('admin.ib-envelope-invitation-image', ['id' => $file->id]);
            }
            $add('hub:'.$file->id, $url, $label, 'invitation');
        }

        return $choices;
    }

    public function resolveEnvelopeImageUrl(string $ref, Invitation $invitation, array $bc = []): string
    {
        $ref = trim($ref);

        if ($ref === '' || $ref === 'none') {
            $legacy = trim((string) ($bc['envelope_image_url'] ?? ''));
            if ($legacy === '') {
                return '';
            }

            return preg_match('#^https?://#i', $legacy)
                ? $legacy
                : asset(ltrim($legacy, '/'));
        }

        if (str_starts_with($ref, 'hub:')) {
            $id = (int) substr($ref, 4);
            if ($id < 1) {
                return '';
            }

            $file = $invitation->hubFiles()
                ->where('id', $id)
                ->where('file_type', Constant::FILE_TYPE['Image'])
                ->first();

            return $file ? (string) ($file->get_path() ?? '') : '';
        }

        if (str_starts_with($ref, 'stock:')) {
            $key = substr($ref, 6);
            if ($key === '') {
                return '';
            }

            $publicFile = public_path('images/invitation-builder/envelopes/'.$key);
            if (is_file($publicFile)) {
                return asset('images/invitation-builder/envelopes/'.$key);
            }

            $item = config('invitation_builder.envelope_images.'.$key);
            if (is_array($item) && ! empty($item['path'])) {
                return asset($item['path']);
            }
        }

        return '';
    }

    public function guessEnvelopeImageRef(string $url, Invitation $invitation): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        foreach ($this->envelopeImageChoices($invitation) as $choice) {
            if ($choice['url'] === $url) {
                return $choice['id'];
            }
        }

        return '';
    }

    protected function draftString(array $data, array $base, string $key): string
    {
        if (! array_key_exists($key, $data)) {
            return (string) ($base[$key] ?? '');
        }

        $value = trim((string) $data[$key]);

        return $value !== '' ? $value : (string) ($base[$key] ?? '');
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

        return $normalized !== [] ? $normalized : (config('invitation_builder.defaults.blocks') ?? ['countdown', 'event_details', 'venue', 'rsvp']);
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
        $themeSlug = $json['theme_slug'] ?? config('invitation_builder.defaults.theme_slug', 'elegant-wedding');
        $renderer = $this->resolveRenderer($themeSlug);
        $template = $renderer === 'builder-wedding' ? 0 : (int) ($invitation->builderSetting?->theme_template ?? 1);
        $ids = $this->resolvePreviewGuestIds($invitation);

        $url = route('user.invitation.show', [
            'invitation_code' => $invitation->code,
            'user_id' => $ids['user_id'],
            'inserted_by' => $ids['inserted_by'],
            'template' => $template > 0 ? $template : 1,
        ]);

        return $url.(str_contains($url, '?') ? '&' : '?').'builder=1';
    }
}
