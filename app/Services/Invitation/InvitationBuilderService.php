<?php

namespace App\Services\Invitation;

use App\Helpers\Constant;
use App\Models\HubFile;
use App\Models\Invitation;
use App\Models\InvitationBuilderSetting;
use App\Models\InvitationBuilderTheme;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvitationBuilderService
{
    /**
     * @return array<string, array<string, mixed>>
     */
    protected function builtinAnimatedThemes(): array
    {
        return config('invitation_builder.animated_themes', []);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function customAnimatedThemes(): array
    {
        return InvitationBuilderTheme::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn (InvitationBuilderTheme $theme) => [
                $theme->slug => $theme->toCatalogEntry(),
            ])
            ->all();
    }

    /**
     * All themes (built-in + uploaded) for resolution and validation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function allAnimatedThemes(): array
    {
        return array_merge($this->builtinAnimatedThemes(), $this->customAnimatedThemes());
    }

    /**
     * Themes shown in the admin theme picker.
     *
     * @return array<string, array<string, mixed>>
     */
    public function animatedThemes(): array
    {
        $custom = $this->customAnimatedThemes();

        if (! config('invitation_builder.show_builtin_animated_themes', true)) {
            return $custom;
        }

        return array_merge($this->builtinAnimatedThemes(), $custom);
    }

    /**
     * @return list<string>
     */
    public function themeSlugs(): array
    {
        return array_keys($this->allAnimatedThemes());
    }

    public function catalog(): array
    {
        $themes = $this->animatedThemes();

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
            'block_field_schemas' => config('invitation_builder.block_field_schemas', []),
            'block_style_fields' => config('invitation_builder.block_style_fields', []),
            'font_weights' => config('invitation_builder.font_weights', []),
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

        $themes = $this->allAnimatedThemes();

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

        $default = config('invitation_builder.defaults.envelope_shape', 'classic');
        if (isset($shapes[$default])) {
            return $default;
        }

        return array_key_first($shapes) ?: 'european';
    }

    public function normalizeThemeSlug(?string $slug): string
    {
        $slug = trim((string) ($slug ?? ''));
        $themes = $this->allAnimatedThemes();

        if ($slug !== '' && isset($themes[$slug])) {
            return $slug;
        }

        $alias = config('invitation_builder.theme_slug_aliases.'.$slug);

        if ($alias && isset($themes[$alias])) {
            return $alias;
        }

        $visible = $this->animatedThemes();
        if ($visible !== []) {
            return array_key_first($visible);
        }

        return config('invitation_builder.defaults.theme_slug', 'opening-gold-bloom');
    }

    /**
     * @return array{url: ?string, type: ?string}
     */
    protected function themeOpeningMedia(?array $themeDef): array
    {
        if (! $themeDef) {
            return ['url' => null, 'type' => null];
        }

        $url = trim((string) ($themeDef['opening_media_url'] ?? $themeDef['opening_video_url'] ?? ''));
        $type = trim((string) ($themeDef['media_type'] ?? ''));

        if ($type === '' && $url !== '') {
            $type = ! empty($themeDef['opening_video_url']) ? 'video' : 'image';
        }

        return [
            'url' => $url !== '' ? $url : null,
            'type' => $type !== '' ? $type : null,
        ];
    }

    protected function themeOpeningVideoUrl(?array $themeDef): ?string
    {
        $media = $this->themeOpeningMedia($themeDef);

        return $media['type'] === 'video' ? $media['url'] : null;
    }

    protected function isLegacyUploadedThemeUrl(string $url): bool
    {
        $path = strtolower(parse_url($url, PHP_URL_PATH) ?: $url);

        return str_contains($path, '/storage/invitation-builder/themes/');
    }

    /**
     * @return array{background_media_url: ?string, video_background: bool, opening_video_url: ?string, hero_media_type: ?string}
     */
    protected function resolveThemeMedia(?array $themeDef, array $json, bool $videoFlag): array
    {
        $themeMedia = $this->themeOpeningMedia($themeDef);
        $opening = $themeMedia['url'];
        $openingType = $themeMedia['type'] ?? 'video';
        $custom = trim((string) ($json['background_media_url'] ?? ''));

        if ($opening !== null && $custom !== '' && $this->isLegacyUploadedThemeUrl($custom)) {
            $custom = '';
        }

        if ($opening !== null && ($custom === '' || $custom === $opening)) {
            return [
                'background_media_url' => $opening,
                'video_background' => $openingType === 'video',
                'opening_video_url' => $openingType === 'video' ? $opening : null,
                'hero_media_type' => $openingType,
            ];
        }

        if ($custom !== '' && ($videoFlag || $opening !== null)) {
            $heroType = $custom === $opening
                ? $openingType
                : ($videoFlag ? 'video' : 'image');

            return [
                'background_media_url' => $custom,
                'video_background' => $heroType === 'video',
                'opening_video_url' => $heroType === 'video' ? $custom : null,
                'hero_media_type' => $heroType,
            ];
        }

        return [
            'background_media_url' => $custom !== '' ? $custom : null,
            'video_background' => $videoFlag,
            'opening_video_url' => $this->themeOpeningVideoUrl($themeDef),
            'hero_media_type' => $custom !== '' ? ($videoFlag ? 'video' : 'image') : $openingType,
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
        $blockData = $this->resolveBlockData($json, $invitation);
        [$blocks, $blockData] = $this->syncBackgroundMusicBlock($blocks, $blockData);

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
            'music_enabled' => $this->hasBackgroundMusicAudio($blockData),
            'video_background' => $themeMedia['video_background'],
            'intro_video_enabled' => (bool) ($json['intro_video_enabled'] ?? ($row?->opening_type === 'intro_video')),
            'logo_url' => $json['logo_url'] ?? null,
            'background_media_url' => $themeMedia['background_media_url'],
            'opening_video_url' => $themeMedia['opening_video_url'],
            'hero_media_type' => $themeMedia['hero_media_type'] ?? null,
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
            'block_data' => $blockData,
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

        $blockData = array_key_exists('block_data', $data)
            ? $this->normalizeBlockData($data['block_data'], $invitation)
            : ($base['block_data'] ?? []);
        [$blocks, $blockData] = $this->syncBackgroundMusicBlock($blocks, $blockData);

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
            'music_enabled' => $this->hasBackgroundMusicAudio($blockData),
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
            'envelope_image_ref' => array_key_exists('envelope_image_ref', $data)
                ? trim((string) $data['envelope_image_ref'])
                : (string) ($base['envelope_image_ref'] ?? ''),
            'opening_headline' => $this->draftString($data, $base, 'opening_headline'),
            'event_date' => $this->draftString($data, $base, 'event_date'),
            'event_time' => $this->draftString($data, $base, 'event_time'),
            'date_position' => $data['date_position'] ?? $base['date_position'],
            'block_accent_color' => $data['block_accent_color'] ?? $base['block_accent_color'],
            'block_floral_border' => $bool('block_floral_border'),
            'blocks' => $blocks,
            'block_data' => $blockData,
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

        $blocks = isset($data['blocks']) ? $this->normalizeBlocks($data['blocks']) : null;
        $blockData = isset($data['block_data'])
            ? $this->normalizeBlockData($data['block_data'], $invitation)
            : null;

        if ($blocks !== null && $blockData !== null) {
            [$blocks, $blockData] = $this->syncBackgroundMusicBlock($blocks, $blockData);
        }

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
            'music_enabled' => $blockData !== null
                ? $this->hasBackgroundMusicAudio($blockData)
                : ($blocks !== null
                    ? $this->musicEnabledFromBlocks($blocks)
                    : filter_var($data['music_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN)),
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
            'blocks' => $blocks,
            'block_data' => $blockData,
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
            fn ($v, $k) => in_array($k, ['blocks', 'block_data'], true)
                ? is_array($v) && $v !== []
                : ($v !== null && $v !== '' && $v !== []),
            ARRAY_FILTER_USE_BOTH
        );

        $setting = InvitationBuilderSetting::query()->updateOrCreate(
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

        $this->syncInvitationPartyFields($invitation, $data, persist: true);

        return $setting;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function syncInvitationPartyFields(Invitation $invitation, array $data, bool $persist = false): Invitation
    {
        $fields = ['groom', 'bride', 'groom_father', 'bride_father'];
        $updates = [];

        foreach ($fields as $field) {
            if (! array_key_exists($field, $data)) {
                continue;
            }

            $value = trim((string) $data[$field]);
            $updates[$field] = $value !== '' ? $value : null;
            $invitation->{$field} = $updates[$field];
        }

        if ($updates !== []) {
            $headline = $this->coupleHeadlineFromParty($invitation);
            if ($headline !== '') {
                $updates['event_name'] = $headline;
                $invitation->event_name = $headline;
            }
        }

        if ($persist && $updates !== []) {
            $invitation->update($updates);
        }

        return $invitation;
    }

    public function coupleHeadlineFromParty(Invitation $invitation): string
    {
        $bride = trim((string) ($invitation->bride ?? ''));
        $groom = trim((string) ($invitation->groom ?? ''));

        if ($bride !== '' && $groom !== '') {
            return $bride.' & '.$groom;
        }

        return $bride !== '' ? $bride : $groom;
    }

    /**
     * @return list<array{id: string, url: string, label: string, group: string}>
     */
    public function envelopeImageChoices(Invitation $invitation): array
    {
        $choices = [];
        $seenIds = [];
        $seenUrls = [];
        $configuredKeys = array_keys(config('invitation_builder.envelope_images', []));

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
            $url = $this->resolveStockEnvelopeUrl($key);
            if ($url === '') {
                continue;
            }
            $label = (string) ($meta['label_ar'] ?? $meta['label_en'] ?? $key);
            $add('stock:'.$key, $url, $label, 'stock');
        }

        $dir = public_path('images/invitation-builder/envelopes');
        if (is_dir($dir)) {
            $files = glob($dir.'/*.{jpg,jpeg,png,webp,gif,svg}', GLOB_BRACE) ?: [];
            foreach ($files as $file) {
                $basename = basename($file);
                $stem = pathinfo($basename, PATHINFO_FILENAME);
                if (in_array($stem, $configuredKeys, true)) {
                    continue;
                }
                $add(
                    'stock:'.$stem,
                    asset('images/invitation-builder/envelopes/'.$basename),
                    $stem,
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
            return $this->resolveStockEnvelopeUrl(substr($ref, 6));
        }

        return '';
    }

    public function resolveStockEnvelopeUrl(string $key): string
    {
        $key = $this->normalizeStockEnvelopeKey($key);
        if ($key === '') {
            return '';
        }

        $fromConfig = $this->resolveStockEnvelopeAssetPath($key, 'path');
        if ($fromConfig !== '') {
            return $fromConfig;
        }

        $dir = 'images/invitation-builder/envelopes/';
        foreach (['svg', 'png', 'jpg', 'jpeg', 'webp', 'gif'] as $ext) {
            $relative = $dir.$key.'.'.$ext;
            if (is_file(public_path($relative))) {
                return asset($relative);
            }
        }

        return '';
    }

    public function resolveStockEnvelopeFlapUrl(string $key): string
    {
        $key = $this->normalizeStockEnvelopeKey($key);
        if ($key === '') {
            return '';
        }

        $flap = $this->resolveStockEnvelopeAssetPath($key, 'flap_path');

        return $flap !== '' ? $flap : $this->resolveStockEnvelopeUrl($key);
    }

    public function resolveStockEnvelopeBodyUrl(string $key): string
    {
        $key = $this->normalizeStockEnvelopeKey($key);
        if ($key === '') {
            return '';
        }

        $body = $this->resolveStockEnvelopeAssetPath($key, 'body_path');

        return $body !== '' ? $body : $this->resolveStockEnvelopeUrl($key);
    }

    protected function resolveStockEnvelopeAssetPath(string $key, string $field): string
    {
        $meta = config('invitation_builder.envelope_images.'.$key);
        if (! is_array($meta) || empty($meta[$field])) {
            return '';
        }

        $relative = (string) $meta[$field];
        if ($relative === '') {
            return '';
        }

        if (is_file(public_path($relative))) {
            return asset($relative);
        }

        return '';
    }

    protected function envelopeBodyClipFromFold(int $foldY, int $foldDepth = 10): string
    {
        $y = max(5, min(75, $foldY));
        $depth = max(2, min(25, $foldDepth));
        $base = min(95, $y + $depth);

        return "polygon(0 {$y}%, 50% {$base}%, 100% {$y}%, 100% 100%, 0 100%)";
    }

    /**
     * @param  array<string, mixed>  $flap
     * @param  array<string, mixed>  $layout
     */
    protected function mergeEnvelopeSizeTune(array $size, array &$layout, bool $mobile = false): void
    {
        $suffix = $mobile ? '_sm' : '';
        $map = [
            'width' => 'envelope_width',
            'height' => 'envelope_height',
            'max_width' => 'envelope_max_width',
            'max_height' => 'envelope_max_height',
            'aspect_ratio' => 'envelope_aspect_ratio',
            'scene_width' => 'scene_width',
            'scene_min_height' => 'scene_min_height',
        ];

        foreach ($map as $configKey => $baseKey) {
            if (! isset($size[$configKey]) || $size[$configKey] === '') {
                continue;
            }

            $value = $size[$configKey];
            if ($configKey === 'aspect_ratio') {
                if (is_array($value) && count($value) === 2) {
                    $value = ((string) $value[0]).' / '.((string) $value[1]);
                } elseif (is_string($value) && str_contains($value, ':') && ! str_contains($value, '/')) {
                    $value = str_replace(':', ' / ', $value);
                }
            }

            $layout[$baseKey.$suffix] = (string) $value;
        }
    }

    /**
     * @param  array<string, mixed>  $size
     * @return array<string, mixed>
     */
    protected function envelopeSizeMobileTune(array $size): array
    {
        $mobile = is_array($size['mobile'] ?? null) ? $size['mobile'] : [];

        foreach ($size as $key => $value) {
            if (! is_string($key) || ! str_ends_with($key, '_mobile')) {
                continue;
            }
            $baseKey = substr($key, 0, -7);
            if ($baseKey !== '') {
                $mobile[$baseKey] = $value;
            }
        }

        return $mobile;
    }

    protected function mergeEnvelopeFlapTune(array $flap, array &$layout, bool $mobile = false): void
    {
        $suffix = $mobile ? '_sm' : '';
        $map = [
            'height' => 'flap_height',
            'top' => 'flap_top',
            'top_offset' => 'flap_top',
            'left' => 'flap_left',
            'width' => 'flap_width',
            'clip_path' => 'flap_clip_path',
            'flap_clip_path' => 'flap_clip_path',
            'transform_origin' => 'flap_transform_origin',
            'image_fit' => 'flap_image_fit',
            'image_position' => 'flap_image_position',
            'image_min_height' => 'flap_image_min_height',
            'open_rotate' => 'flap_open_rotate',
            'body_clip_path' => 'body_clip_path',
        ];

        foreach ($map as $configKey => $baseKey) {
            if (! isset($flap[$configKey]) || $flap[$configKey] === '') {
                continue;
            }

            $value = $flap[$configKey];
            if ($configKey === 'open_rotate') {
                $value = is_numeric($value) ? ((string) $value).'deg' : (string) $value;
            } elseif ($configKey === 'height' && is_numeric($value)) {
                $value = ((string) $value).'%';
            }

            if ($baseKey === 'flap_image_fit' && ! in_array($value, ['contain', 'cover'], true)) {
                continue;
            }

            $layout[$baseKey.$suffix] = (string) $value;
        }
    }

    /**
     * @param  array<string, mixed>  $flap
     * @return array<string, mixed>
     */
    protected function envelopeFlapMobileTune(array $flap): array
    {
        $mobile = is_array($flap['mobile'] ?? null) ? $flap['mobile'] : [];

        foreach ($flap as $key => $value) {
            if (! is_string($key) || ! str_ends_with($key, '_mobile')) {
                continue;
            }
            $baseKey = substr($key, 0, -7);
            if ($baseKey !== '') {
                $mobile[$baseKey] = $value;
            }
        }

        return $mobile;
    }

    public function envelopeMobileBreakpoint(): int
    {
        $breakpoint = (int) config('invitation_builder.envelope_mobile_breakpoint', 767);

        return max(320, min(1200, $breakpoint));
    }

    public function stockEnvelopeImageFit(?string $ref): string
    {
        return $this->stockEnvelopePhotoLayout($ref)['body_fit'];
    }

    /**
     * Photo envelope layout (body + flap) for stock images. Hub uploads use defaults.
     *
     * @return array{
     *     body_fit: string,
     *     body_position: string,
     *     body_clip_path: string,
     *     show_pocket_liner: bool,
     *     body_image_url: string,
     *     flap_image_url: string,
     *     has_separate_flap: bool,
     *     has_body_flap_split: bool,
     *     flap_height: string,
     *     flap_top: string,
     *     flap_left: string,
     *     flap_width: string,
     *     flap_clip_path: string,
     *     flap_transform_origin: string,
     *     flap_image_fit: string,
     *     flap_image_position: string,
     *     flap_image_min_height: string,
     *     flap_open_rotate: string,
     *     stock_slug: string,
     *     envelope_width: string,
     *     envelope_height: string,
     *     envelope_max_width: string,
     *     envelope_max_height: string,
     *     envelope_aspect_ratio: string,
     *     scene_width: string,
     *     scene_min_height: string
     * }
     */
    public function stockEnvelopePhotoLayout(?string $ref): array
    {
        $defaults = config('invitation_builder.envelope_image_defaults', []);
        $layout = [
            'body_fit' => in_array($defaults['body_fit'] ?? 'contain', ['contain', 'cover'], true)
                ? ($defaults['body_fit'] ?? 'contain')
                : 'contain',
            'body_position' => (string) ($defaults['body_position'] ?? 'center'),
            'body_clip_path' => (string) ($defaults['body_clip_path'] ?? ''),
            'show_pocket_liner' => (bool) ($defaults['show_pocket_liner'] ?? true),
            'body_image_url' => '',
            'flap_image_url' => '',
            'has_separate_flap' => false,
            'has_body_flap_split' => false,
            'envelope_width' => (string) ($defaults['envelope_width'] ?? ''),
            'envelope_height' => (string) ($defaults['envelope_height'] ?? ''),
            'envelope_max_width' => (string) ($defaults['envelope_max_width'] ?? 'min(92vw, 420px)'),
            'envelope_max_height' => (string) ($defaults['envelope_max_height'] ?? 'min(90dvh, 520px)'),
            'envelope_aspect_ratio' => (string) ($defaults['envelope_aspect_ratio'] ?? '4 / 5.2'),
            'scene_width' => (string) ($defaults['scene_width'] ?? 'min(92vw, 440px)'),
            'scene_min_height' => (string) ($defaults['scene_min_height'] ?? 'min(420px, calc(100dvh - 118px))'),
            'flap_height' => (string) ($defaults['flap_height'] ?? '54%'),
            'flap_top' => (string) ($defaults['flap_top'] ?? '-8px'),
            'flap_left' => (string) ($defaults['flap_left'] ?? '0'),
            'flap_width' => (string) ($defaults['flap_width'] ?? '100%'),
            'flap_clip_path' => (string) ($defaults['flap_clip_path'] ?? 'polygon(0 0, 50% 100%, 100% 0)'),
            'flap_transform_origin' => (string) ($defaults['flap_transform_origin'] ?? '50% 0%'),
            'flap_image_fit' => in_array($defaults['flap_image_fit'] ?? 'cover', ['contain', 'cover'], true)
                ? ($defaults['flap_image_fit'] ?? 'cover')
                : 'cover',
            'flap_image_position' => (string) ($defaults['flap_image_position'] ?? 'center top'),
            'flap_image_min_height' => (string) ($defaults['flap_image_min_height'] ?? '185%'),
            'flap_open_rotate' => (string) ($defaults['flap_open_rotate'] ?? '-168deg'),
            'stock_slug' => '',
            'has_mobile_flap_tune' => false,
            'has_mobile_size_tune' => false,
            'mobile_breakpoint' => $this->envelopeMobileBreakpoint(),
        ];

        $ref = trim((string) $ref);
        if (! str_starts_with($ref, 'stock:')) {
            return $layout;
        }

        $key = $this->normalizeStockEnvelopeKey(substr($ref, 6));
        $layout['stock_slug'] = $key;

        $meta = config('invitation_builder.envelope_images.'.$key);
        if (! is_array($meta)) {
            return $layout;
        }

        $layout['body_image_url'] = $this->resolveStockEnvelopeBodyUrl($key);
        $layout['flap_image_url'] = $this->resolveStockEnvelopeFlapUrl($key);
        $layout['has_separate_flap'] = $this->resolveStockEnvelopeAssetPath($key, 'flap_path') !== '';

        if (! empty($meta['image_fit']) && in_array($meta['image_fit'], ['contain', 'cover'], true)) {
            $layout['body_fit'] = $meta['image_fit'];
        }
        if (! empty($meta['body_position'])) {
            $layout['body_position'] = (string) $meta['body_position'];
        }
        if (! empty($meta['body_clip_path'])) {
            $layout['body_clip_path'] = (string) $meta['body_clip_path'];
        }
        if (array_key_exists('show_pocket_liner', $meta)) {
            $layout['show_pocket_liner'] = (bool) $meta['show_pocket_liner'];
        }

        $size = is_array($meta['size'] ?? null) ? $meta['size'] : [];
        $this->mergeEnvelopeSizeTune($size, $layout, false);

        $mobileSize = $this->envelopeSizeMobileTune($size);
        if ($mobileSize !== []) {
            $this->mergeEnvelopeSizeTune($mobileSize, $layout, true);
            $layout['has_mobile_size_tune'] = true;
        }

        $flap = is_array($meta['flap'] ?? null) ? $meta['flap'] : [];
        $this->mergeEnvelopeFlapTune($flap, $layout, false);

        $mobileFlap = $this->envelopeFlapMobileTune($flap);
        if ($mobileFlap !== []) {
            $this->mergeEnvelopeFlapTune($mobileFlap, $layout, true);
            $layout['has_mobile_flap_tune'] = true;
        }

        if ($layout['body_clip_path'] === '' && isset($flap['fold_y']) && is_numeric($flap['fold_y'])) {
            $foldDepth = isset($flap['fold_depth']) && is_numeric($flap['fold_depth'])
                ? (int) $flap['fold_depth']
                : 10;
            $layout['body_clip_path'] = $this->envelopeBodyClipFromFold((int) $flap['fold_y'], $foldDepth);
            if (! isset($flap['height'])) {
                $layout['flap_height'] = ((int) $flap['fold_y']).'%';
            }
        }

        $bodyClipSm = (string) ($layout['body_clip_path_sm'] ?? '');
        if ($bodyClipSm === '' && isset($mobileFlap['fold_y']) && is_numeric($mobileFlap['fold_y'])) {
            $foldDepth = isset($mobileFlap['fold_depth']) && is_numeric($mobileFlap['fold_depth'])
                ? (int) $mobileFlap['fold_depth']
                : (isset($flap['fold_depth']) && is_numeric($flap['fold_depth']) ? (int) $flap['fold_depth'] : 10);
            $layout['body_clip_path_sm'] = $this->envelopeBodyClipFromFold((int) $mobileFlap['fold_y'], $foldDepth);
            $layout['has_mobile_flap_tune'] = true;
            if (! isset($mobileFlap['height'])) {
                $layout['flap_height_sm'] = ((int) $mobileFlap['fold_y']).'%';
            }
        }

        if ($layout['flap_clip_path'] !== ($defaults['flap_clip_path'] ?? 'polygon(0 0, 50% 100%, 100% 0)')
            && $layout['body_clip_path'] === ''
            && ! $layout['has_separate_flap']) {
            $layout['has_body_flap_split'] = true;
        }

        if ($layout['body_clip_path'] !== '' || $layout['has_separate_flap']) {
            $layout['has_body_flap_split'] = true;
        }

        return $layout;
    }

    protected function normalizeStockEnvelopeKey(string $key): string
    {
        $key = trim($key);
        if ($key === '') {
            return '';
        }

        $configured = config('invitation_builder.envelope_images', []);
        if (isset($configured[$key])) {
            return $key;
        }

        $stem = pathinfo($key, PATHINFO_FILENAME);

        return isset($configured[$stem]) ? $stem : $stem;
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

    /**
     * @return array<string, array<string, mixed>>
     */
    public function blockFieldSchemas(): array
    {
        return config('invitation_builder.block_field_schemas', []);
    }

    /**
     * @param  array<string, mixed>  $json
     * @return array<string, array<string, mixed>>
     */
    public function resolveBlockData(array $json, Invitation $invitation): array
    {
        $stored = is_array($json['block_data'] ?? null) ? $json['block_data'] : [];

        return $this->normalizeBlockData(
            $this->mergeLegacyBlockFields($stored, $json, $invitation)
        );
    }

    /**
     * @param  array<string, mixed>  $stored
     * @param  array<string, mixed>  $json
     * @return array<string, mixed>
     */
    protected function mergeLegacyBlockFields(array $stored, array $json, Invitation $invitation): array
    {
        if (! isset($stored['event_details'])) {
            $stored['event_details'] = [];
        }
        if (empty($stored['event_details']['label']) && ! empty($json['details_section_label'])) {
            $stored['event_details']['label'] = $json['details_section_label'];
        }
        if (empty($stored['event_details']['title']) && ! empty($json['details_section_title'])) {
            $stored['event_details']['title'] = $json['details_section_title'];
        }
        if (empty($stored['event_details']['title']) && ! empty($invitation->event_name)) {
            $stored['event_details']['title'] = $invitation->event_name;
        }

        if (! isset($stored['venue'])) {
            $stored['venue'] = [];
        }
        if (empty($stored['venue']['label']) && ! empty($json['venue_section_label'])) {
            $stored['venue']['label'] = $json['venue_section_label'];
        }
        if (empty($stored['venue']['title']) && ! empty($json['venue_section_title'])) {
            $stored['venue']['title'] = $json['venue_section_title'];
        }
        if (empty($stored['venue']['title']) && ! empty($json['venue_name'])) {
            $stored['venue']['title'] = $json['venue_name'];
        }
        if (empty($stored['venue']['description']) && ! empty($json['venue_description'])) {
            $stored['venue']['description'] = $json['venue_description'];
        }

        return $stored;
    }

    public function blockFieldColumnClass(string $type): string
    {
        return match ($type) {
            'textarea' => 'col-12',
            'checkbox' => 'col-md-6',
            'color', 'optional_color' => 'col-md-4',
            'font', 'font_weight', 'select', 'icon_upload', 'audio_upload' => 'col-md-6',
            'font_size' => 'col-md-4',
            'date', 'time', 'datetime-local' => 'col-md-4',
            default => 'col-md-6',
        };
    }

    public function storeBlockIcon(Invitation $invitation, UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'png');
        $filename = 'icon-'.Str::uuid()->toString().'.'.$extension;
        $directory = 'invitation-builder/block-icons/'.$invitation->id;

        Storage::disk('public')->putFileAs($directory, $file, $filename);

        return Storage::disk('public')->url($directory.'/'.$filename);
    }

    public function storeBlockAudio(Invitation $invitation, UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'mp3');
        $filename = 'audio-'.Str::uuid()->toString().'.'.$extension;
        $directory = 'invitation-builder/block-audio/'.$invitation->id;

        Storage::disk('public')->putFileAs($directory, $file, $filename);

        return Storage::disk('public')->url($directory.'/'.$filename);
    }

    public function musicEnabledFromBlocks(array $blocks): bool
    {
        return in_array('background_music', $blocks, true);
    }

    public function hasBackgroundMusicAudio(array $blockData): bool
    {
        $url = trim((string) ($blockData['background_music']['audio_url'] ?? ''));

        return $url !== '' && (bool) preg_match('#^(https?://|/)#i', $url);
    }

    /**
     * @return array{0: array<int, string>, 1: array<string, mixed>}
     */
    public function syncBackgroundMusicBlock(array $blocks, array $blockData): array
    {
        if ($this->hasBackgroundMusicAudio($blockData) && ! in_array('background_music', $blocks, true)) {
            $blocks[] = 'background_music';
        }

        return [$blocks, $blockData];
    }

    public function persistBackgroundMusicAudio(Invitation $invitation, string $audioUrl): void
    {
        $audioUrl = trim($audioUrl);
        if ($audioUrl === '' || ! preg_match('#^(https?://|/)#i', $audioUrl)) {
            return;
        }

        $row = $invitation->builderSetting;
        if (! $row) {
            return;
        }

        $json = is_array($row->settings) ? $row->settings : [];
        $blocks = $this->normalizeBlocks($json['blocks'] ?? []);
        $blockData = $this->resolveBlockData($json, $invitation);
        $blockData['background_music']['audio_url'] = $audioUrl;
        [$blocks, $blockData] = $this->syncBackgroundMusicBlock($blocks, $blockData);

        $json['blocks'] = $blocks;
        $json['block_data'] = $blockData;
        $json['music_enabled'] = true;

        $row->update(['settings' => $json]);
        $invitation->unsetRelation('builderSetting');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function resolveSelectOptions(array $fieldDef): array
    {
        $options = $fieldDef['options'] ?? [];
        if (is_string($options) && $options !== '') {
            $options = config('invitation_builder.'.$options, []);
        }

        return is_array($options) ? $options : [];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function blockSchemaFields(array $schema): array
    {
        $fields = $schema['fields'] ?? [];

        foreach ($schema['groups'] ?? [] as $groupDef) {
            foreach ($groupDef['fields'] ?? [] as $fieldKey => $fieldDef) {
                $fields[$fieldKey] = $fieldDef;
            }
        }

        return $fields;
    }

    public function formatBlockFieldForInput(string $type, mixed $value): string
    {
        if ($type === 'checkbox') {
            return '';
        }

        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return '';
        }

        if ($type === 'font_size' && preg_match('/^(\d+(?:\.\d+)?)px$/i', $value, $matches)) {
            return $matches[1];
        }

        try {
            return match ($type) {
                'time' => \Carbon\Carbon::parse($value)->format('H:i'),
                'date' => \Carbon\Carbon::parse($value)->format('Y-m-d'),
                'datetime-local' => \Carbon\Carbon::parse($value)->format('Y-m-d\TH:i'),
                default => $value,
            };
        } catch (\Throwable) {
            if ($type === 'time' && preg_match('/^\d{1,2}:\d{2}/', $value)) {
                return substr($value, 0, 5);
            }

            return $value;
        }
    }

    public function formatBlockFieldForDisplay(string $type, mixed $value): string
    {
        if ($type === 'checkbox') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '';
        }

        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return '';
        }

        $locale = app()->getLocale() ?: 'ar';

        try {
            return match ($type) {
                'time' => \Carbon\Carbon::parse($value)->locale($locale)->translatedFormat('h:i A'),
                'date' => \Carbon\Carbon::parse($value)->locale($locale)->translatedFormat('j F Y'),
                'datetime-local' => \Carbon\Carbon::parse($value)->locale($locale)->translatedFormat('j F Y · h:i A'),
                default => $value,
            };
        } catch (\Throwable) {
            return $value;
        }
    }

    public function normalizeBlockStyleFieldValue(string $type, mixed $value): string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return '';
        }

        if ($type === 'font') {
            $allowed = array_keys(config('invitation_builder.fonts', []));

            return in_array($value, $allowed, true) ? $value : '';
        }

        if ($type === 'optional_color') {
            if (! preg_match('/^#?[0-9A-Fa-f]{6}$/', $value)) {
                return '';
            }

            return str_starts_with($value, '#') ? strtoupper($value) : '#'.strtoupper($value);
        }

        if ($type === 'font_size') {
            if (preg_match('/^(\d+(?:\.\d+)?)\s*px$/i', $value, $matches)) {
                $value = $matches[1];
            }
            if (! is_numeric($value)) {
                return '';
            }
            $px = (float) $value;
            if ($px < 8 || $px > 120) {
                return '';
            }

            return rtrim(rtrim(number_format($px, 1, '.', ''), '0'), '.').'px';
        }

        if ($type === 'font_weight') {
            $allowed = array_keys(config('invitation_builder.font_weights', []));

            return in_array($value, $allowed, true) ? $value : '';
        }

        return $value;
    }

    public function normalizeBlockFieldValue(string $type, mixed $value, array $fieldDef = []): mixed
    {
        if ($type === 'checkbox') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return '';
        }

        if ($type === 'select') {
            $options = $this->resolveSelectOptions($fieldDef);

            return array_key_exists($value, $options) ? $value : '';
        }

        if ($type === 'url' || $type === 'icon_upload' || $type === 'audio_upload') {
            if (preg_match('#^(https?://|/)#i', $value)) {
                return $value;
            }

            return '';
        }

        try {
            return match ($type) {
                'time' => \Carbon\Carbon::parse($value)->format('H:i'),
                'date' => \Carbon\Carbon::parse($value)->format('Y-m-d'),
                'datetime-local' => \Carbon\Carbon::parse($value)->format('Y-m-d\TH:i'),
                'number' => is_numeric($value) ? $value : $value,
                default => $value,
            };
        } catch (\Throwable) {
            return $value;
        }
    }

    /**
     * @param  mixed  $input
     * @return array<string, array<string, mixed>>
     */
    public function normalizeBlockData(mixed $input, ?Invitation $invitation = null): array
    {
        $schemas = $this->blockFieldSchemas();
        $raw = is_array($input) ? $input : [];
        $normalized = [];

        foreach ($schemas as $blockKey => $schema) {
            $blockInput = is_array($raw[$blockKey] ?? null) ? $raw[$blockKey] : [];
            $blockOut = [];

            foreach ($this->blockSchemaFields($schema) as $fieldKey => $fieldDef) {
                $fieldType = $fieldDef['type'] ?? 'text';
                $value = $blockInput[$fieldKey] ?? null;
                if (is_string($value)) {
                    $value = trim($value);
                }
                if ($value === null || $value === '') {
                    $value = $fieldDef['default'] ?? '';
                    if ($blockKey === 'event_details' && $fieldKey === 'title' && $invitation && $value === '') {
                        $value = $invitation->event_name ?? '';
                    }
                    if ($blockKey === 'venue' && $fieldKey === 'title' && $invitation && $value === '') {
                        $value = $invitation->event_name ?? '';
                    }
                }
                $blockOut[$fieldKey] = $this->normalizeBlockFieldValue($fieldType, $value, $fieldDef);
            }

            foreach (config('invitation_builder.block_style_fields', []) as $styleKey => $styleDef) {
                $styleType = $styleDef['type'] ?? 'text';
                $styleValue = $blockInput[$styleKey] ?? '';
                $blockOut[$styleKey] = $this->normalizeBlockStyleFieldValue($styleType, $styleValue);
            }

            foreach ($schema['repeaters'] ?? [] as $repeaterKey => $repeaterDef) {
                $rows = is_array($blockInput[$repeaterKey] ?? null) ? $blockInput[$repeaterKey] : [];
                $max = (int) ($repeaterDef['max'] ?? 12);
                $cleanRows = [];

                foreach ($rows as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $cleanRow = [];
                    $hasContent = false;
                    foreach ($repeaterDef['fields'] ?? [] as $rfKey => $rfDef) {
                        $rfType = $rfDef['type'] ?? 'text';
                        $rv = $this->normalizeBlockFieldValue($rfType, $row[$rfKey] ?? '', $rfDef);
                        $cleanRow[$rfKey] = $rv;
                        if ($rfType === 'checkbox') {
                            if ($rv) {
                                $hasContent = true;
                            }
                            continue;
                        }
                        if ((string) $rv !== '') {
                            $hasContent = true;
                        }
                    }
                    if ($hasContent) {
                        $cleanRows[] = $cleanRow;
                    }
                    if (count($cleanRows) >= $max) {
                        break;
                    }
                }
                $blockOut[$repeaterKey] = $cleanRows;
            }

            $normalized[$blockKey] = $blockOut;
        }

        return $normalized;
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

    public function builderDisplayGuest(Invitation $invitation, ?int $userId = null): User
    {
        $invitation->loadMissing('users');

        if ($userId !== null && $userId > 0) {
            $guest = $this->resolveGuestForShow($invitation, $userId, null, true);

            if ($guest) {
                return $guest;
            }

            $ids = $this->resolvePreviewGuestIds($invitation);
            $guest = $this->resolveGuestForShow($invitation, $userId, (int) $ids['inserted_by'], true);

            if ($guest) {
                return $guest;
            }
        }

        $ids = $this->resolvePreviewGuestIds($invitation);
        $guest = $this->resolveGuestForShow(
            $invitation,
            (int) $ids['user_id'],
            (int) $ids['inserted_by'],
            true
        );

        if ($guest) {
            return $guest;
        }

        return $this->syntheticPreviewUser($invitation);
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
        $ids = $this->resolvePreviewGuestIds($invitation);

        return $this->guestInvitationUrl(
            $invitation,
            (int) $ids['user_id'],
            (int) $ids['inserted_by'],
            preview: true
        );
    }

    /**
     * Full-page guest invitation URL (no admin preview chrome).
     * Returns null when the invitation has no builder settings.
     */
    public function directGuestInvitationUrl(Invitation $invitation): ?string
    {
        $invitation->loadMissing('builderSetting');
        $builderRow = $invitation->builderSetting;

        if (! $builderRow) {
            return null;
        }

        $url = route('user.invitation.builder.show', [
            'invitation_code' => $invitation->code,
        ]);

        if (! $builderRow->isPublished()) {
            $url .= (str_contains($url, '?') ? '&' : '?').'builder=1';
        }

        return $url;
    }

    /**
     * Guest-facing invitation URL for SMS, WhatsApp, and API responses.
     * Uses the builder-rendered page when invitation_builder_settings exist.
     */
    public function guestInvitationUrl(
        Invitation $invitation,
        int $userId,
        ?int $insertedBy = null,
        bool $preview = false
    ): string {
        $invitation->loadMissing('builderSetting');
        $builderRow = $invitation->builderSetting;

        if ($builderRow) {
            $routeParams = ['invitation_code' => $invitation->code];
            if ($userId > 0) {
                $routeParams['user_id'] = $userId;
            }

            $url = route('user.invitation.builder.show', $routeParams);

            if ($preview || ! $builderRow->isPublished()) {
                $url .= (str_contains($url, '?') ? '&' : '?').'builder=1';
            }

            return $url;
        }

        $routeParams = array_filter([
            'invitation_code' => $invitation->code,
            'user_id' => $userId,
            'inserted_by' => $insertedBy,
        ], fn ($value) => $value !== null && $value !== '');

        return route('user.invitation.show', $routeParams);
    }
}