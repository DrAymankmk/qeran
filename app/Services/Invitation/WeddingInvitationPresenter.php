<?php

namespace App\Services\Invitation;

use App\Models\Category;
use App\Models\Invitation;
use Carbon\Carbon;

class WeddingInvitationPresenter
{
    public static function from(Invitation $invitation, array $bc, ?string $hostName = null, ?Category $category = null): array
    {
        $locale = app()->getLocale() ?: 'ar';

        $name1 = $invitation->bride ?: ($invitation->groom ?: strtok((string) ($bc['opening_headline'] ?? $invitation->event_name), '&'));
        $name2 = ($invitation->bride && $invitation->groom)
            ? $invitation->groom
            : ($hostName ?? $invitation->host_name ?? '');

        if ($name2 === '' && str_contains((string) $name1, '&')) {
            $parts = array_map('trim', explode('&', (string) $name1));
            $name1 = $parts[0] ?? $name1;
            $name2 = $parts[1] ?? '';
        }

        $initials = trim((string) ($bc['envelope_initials'] ?? ''));
        if ($initials === '' && $invitation->groom && $invitation->bride) {
            $initials = mb_substr(trim($invitation->groom), 0, 1).' & '.mb_substr(trim($invitation->bride), 0, 1);
        } elseif ($initials === '' && $name1 && $name2) {
            $initials = mb_substr($name1, 0, 1).' & '.mb_substr($name2, 0, 1);
        }

        $eventDate = self::firstNonEmpty(
            $bc['event_date'] ?? null,
            $invitation->date
        );
        $eventTime = self::firstNonEmpty(
            $bc['event_time'] ?? null,
            $invitation->time
        );

        $dateCarbon = self::parseFlexibleDate($eventDate);
        $timeCarbon = self::parseFlexibleTime($eventTime, $dateCarbon);

        $envelopeHex = config('invitation_builder.envelope_colors.'.$bc['envelope_color'].'.hex', '#f5f0e6');

        $heroMediaType = trim((string) ($bc['hero_media_type'] ?? ''));
        $heroMediaUrl = trim((string) ($bc['background_media_url'] ?? ''));

        if ($heroMediaUrl === '' && ! empty($bc['video_background'])) {
            $heroMediaUrl = trim((string) ($bc['opening_video_url'] ?? ''));
            $heroMediaType = $heroMediaUrl !== '' ? 'video' : $heroMediaType;
        }

        if ($heroMediaUrl === '') {
            $themeDef = app(InvitationBuilderService::class)->themeDefinition($bc['theme_slug'] ?? null);
            $heroMediaUrl = trim((string) ($themeDef['opening_media_url'] ?? $themeDef['opening_video_url'] ?? ''));
            $heroMediaType = trim((string) ($themeDef['media_type'] ?? ''));
            if ($heroMediaType === '' && $heroMediaUrl !== '') {
                $heroMediaType = ! empty($themeDef['opening_video_url']) ? 'video' : 'image';
            }
        }

        if ($heroMediaType === '') {
            $heroMediaType = ! empty($bc['video_background']) ? 'video' : 'image';
        }

        $heroVideoUrl = $heroMediaType === 'video' ? $heroMediaUrl : '';
        $heroImageUrl = in_array($heroMediaType, ['image', 'gif'], true) ? $heroMediaUrl : '';

        $envelopeImageUrl = app(InvitationBuilderService::class)->resolveEnvelopeImageUrl(
            (string) ($bc['envelope_image_ref'] ?? ''),
            $invitation,
            $bc
        );

        $countdownIso = '2026-12-31T12:00:00';
        if ($dateCarbon) {
            try {
                $countdownIso = Carbon::parse(
                    $dateCarbon->format('Y-m-d').' '.($timeCarbon ? $timeCarbon->format('H:i') : '12:00')
                )->toIso8601String();
            } catch (\Throwable) {
                $countdownIso = $dateCarbon->copy()->endOfDay()->toIso8601String();
            }
        }

        $venueName = trim((string) ($bc['venue_name'] ?? $invitation->event_name ?? ''));
        $venueLocation = trim((string) ($bc['venue_location'] ?? ''));
        $invitationAddress = trim((string) ($invitation->address ?? ''));

        if ($venueLocation === '') {
            $venueLocation = $invitationAddress;
        }

        $mapQuery = self::resolveMapQuery($invitation, $venueLocation, $venueName);
        $mapUrl = self::buildMapLink($mapQuery);
        $mapEmbedUrl = self::buildMapEmbedUrl($mapQuery, $locale);

        $venueAddressLine = $venueLocation;
        if ($invitationAddress !== '' && $invitationAddress !== $venueLocation) {
            $venueAddressLine = trim($venueLocation."\n".$invitationAddress);
        }

        return [
            'bc' => $bc,
            'blocks' => $bc['blocks'] ?? ['countdown', 'event_details', 'venue', 'rsvp'],
            'wiName1' => $name1,
            'wiName2' => $name2,
            'wiNamesFooter' => trim($name1.' & '.$name2, ' &'),
            'wiInitials' => $initials,
            'wiHostLabel' => $hostName ?? $invitation->host_name ?? '',
            'wiSubtitle' => $category?->getTranslation('ar')?->name ?? 'نتشرف بدعوتكم لحضور حفل الزفاف',
            'wiHeadline' => $bc['opening_headline']
                ?? app(InvitationBuilderService::class)->coupleHeadlineFromParty($invitation)
                ?: $invitation->event_name,
            'wiGroom' => trim((string) ($invitation->groom ?? '')),
            'wiBride' => trim((string) ($invitation->bride ?? '')),
            'wiGroomFather' => trim((string) ($invitation->groom_father ?? '')),
            'wiBrideFather' => trim((string) ($invitation->bride_father ?? '')),
            'wiDateBadge' => $dateCarbon ? $dateCarbon->locale($locale)->translatedFormat('j F Y') : '',
            'wiDateMain' => $dateCarbon ? $dateCarbon->locale($locale)->translatedFormat('j F').'<br>'.$dateCarbon->format('Y') : '',
            'wiDayName' => $dateCarbon ? $dateCarbon->locale($locale)->translatedFormat('l') : '',
            'wiHeroDetail' => trim(
                ($dateCarbon ? $dateCarbon->locale($locale)->translatedFormat('j F Y') : '')
                .($timeCarbon ? ' · '.$timeCarbon->format('h:i A') : '')
                .($invitation->address ? '<br>'.$invitation->address : '')
            ),
            'wiCeremonyTime' => $timeCarbon ? $timeCarbon->format('h:i A') : '—',
            'wiCeremonyNote' => $bc['ceremony_note'] ?? ($invitation->description ? mb_substr(strip_tags($invitation->description), 0, 80) : ''),
            'wiReceptionTime' => ! empty($bc['reception_time'])
                ? (strlen((string) $bc['reception_time']) <= 8
                    ? Carbon::parse($bc['reception_time'])->format('h:i A')
                    : $bc['reception_time'])
                : '',
            'wiReceptionNote' => $bc['reception_note'] ?? '',
            'wiVenueName' => $venueName !== '' ? $venueName : '—',
            'wiVenueLocation' => $venueLocation,
            'wiVenueAddressLine' => $venueAddressLine,
            'wiVenueTitle' => self::blockValue($bc, 'venue', 'title', $bc['venue_section_title'] ?? ($venueName !== '' ? $venueName : ($invitation->event_name ?: 'موقع الحفل'))),
            'wiVenueLabel' => self::blockValue($bc, 'venue', 'label', $bc['venue_section_label'] ?? 'موقع الحفل'),
            'wiVenueDescription' => self::blockValue($bc, 'venue', 'description', trim((string) ($bc['venue_description'] ?? ''))),
            'wiHasMap' => $mapQuery !== null,
            'wiMapEmbedUrl' => $mapEmbedUrl,
            'wiDetailsTitle' => self::blockValue($bc, 'event_details', 'title', $bc['details_section_title'] ?? ($invitation->event_name ?: 'تفاصيل الحفل')),
            'wiDetailsLabel' => self::blockValue($bc, 'event_details', 'label', $bc['details_section_label'] ?? 'جميع التفاصيل'),
            'wiBlockData' => $bc['block_data'] ?? [],
            'wiCountdownIso' => $countdownIso,
            'wiMapUrl' => $mapUrl,
            'wiEnvelopeHex' => $envelopeHex,
            'wiEnvelopeShape' => app(InvitationBuilderService::class)->normalizeEnvelopeShape($bc['envelope_shape'] ?? null),
            'wiEnvelopeImageUrl' => $envelopeImageUrl,
            'wiEnvelopeHasImage' => $envelopeImageUrl !== '',
            'wiSealStyle' => $bc['seal_style'] ?? 'wax_classic',
            ...self::sealViewVars($bc['seal_style'] ?? 'wax_classic', $bc['seal_color'] ?? null),
            'wiDatePosition' => $bc['date_position'] ?? 'center',
            'wiHeroVideoUrl' => $heroVideoUrl,
            'wiHeroHasVideo' => $heroVideoUrl !== '',
            'wiHeroImageUrl' => $heroImageUrl,
            'wiHeroHasImage' => $heroImageUrl !== '',
            'wiHeroMediaType' => $heroMediaType,
            'showEnvelope' => ($bc['opening_type'] ?? 'envelope') === 'envelope',
        ];
    }

    public static function hasBlock(array $blocks, string $key): bool
    {
        return in_array($key, $blocks, true);
    }

    /**
     * @return array{class: string, style: string}
     */
    public static function blockStyleAttributes(array $bc, string $blockKey): array
    {
        $data = is_array($bc['block_data'][$blockKey] ?? null) ? $bc['block_data'][$blockKey] : [];
        $styleParts = [];
        $allowedFonts = array_keys(config('invitation_builder.fonts', []));

        $bg = trim((string) ($data['background_color'] ?? ''));
        if ($bg !== '' && preg_match('/^#?[0-9A-Fa-f]{6}$/i', $bg)) {
            $hex = str_starts_with($bg, '#') ? $bg : '#'.$bg;
            $styleParts[] = '--wi-block-bg: '.$hex;
        }

        $font = trim((string) ($data['font_family'] ?? ''));
        if ($font !== '' && in_array($font, $allowedFonts, true)) {
            $styleParts[] = "font-family: '{$font}', 'Cairo', sans-serif";
        }

        $headline = trim((string) ($data['headline_font'] ?? ''));
        $headlineEffective = ($headline !== '' && in_array($headline, $allowedFonts, true)) ? $headline : $font;
        if ($headlineEffective !== '' && in_array($headlineEffective, $allowedFonts, true)) {
            $styleParts[] = "--wi-block-headline-font: '{$headlineEffective}'";
        }

        $typographyVars = [
            'title_font_size' => '--wi-block-title-size',
            'title_font_weight' => '--wi-block-title-weight',
            'title_color' => '--wi-block-title-color',
            'label_font_size' => '--wi-block-label-size',
            'label_font_weight' => '--wi-block-label-weight',
            'label_color' => '--wi-block-label-color',
            'body_font_size' => '--wi-block-body-size',
            'body_font_weight' => '--wi-block-body-weight',
            'body_color' => '--wi-block-body-color',
        ];

        foreach ($typographyVars as $fieldKey => $cssVar) {
            $raw = trim((string) ($data[$fieldKey] ?? ''));
            if ($raw === '') {
                continue;
            }
            if (str_ends_with($fieldKey, '_color') && preg_match('/^#?[0-9A-Fa-f]{6}$/i', $raw)) {
                $hex = str_starts_with($raw, '#') ? $raw : '#'.$raw;
                $styleParts[] = "{$cssVar}: {$hex}";
            } elseif (str_ends_with($fieldKey, '_font_size') && preg_match('/^\d+(?:\.\d+)?px$/', $raw)) {
                $styleParts[] = "{$cssVar}: {$raw}";
            } elseif (str_ends_with($fieldKey, '_font_weight') && in_array($raw, array_keys(config('invitation_builder.font_weights', [])), true)) {
                $styleParts[] = "{$cssVar}: {$raw}";
            }
        }

        return [
            'class' => 'wi-block-custom wi-block-'.$blockKey,
            'style' => implode('; ', $styleParts),
        ];
    }

    public static function blockValue(array $bc, string $block, string $key, mixed $default = ''): mixed
    {
        $data = $bc['block_data'][$block] ?? [];
        if (! is_array($data) || ! array_key_exists($key, $data)) {
            return $default;
        }

        $value = $data[$key];
        if ($value === null || $value === '') {
            return $default;
        }

        return $value;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function blockRepeater(array $bc, string $block, string $repeater): array
    {
        $rows = $bc['block_data'][$block][$repeater] ?? [];

        return is_array($rows) ? array_values($rows) : [];
    }

    public static function formatBlockDisplay(string $type, mixed $value): string
    {
        return app(InvitationBuilderService::class)->formatBlockFieldForDisplay($type, $value);
    }

    /**
     * @return array<string, string>
     */
    public static function blockSectionPartials(): array
    {
        return [
            'countdown' => 'builder-wedding-section-countdown',
            'our_story' => 'builder-wedding-section-our-story',
            'event_details' => 'builder-wedding-section-details',
            'gallery' => 'builder-wedding-section-gallery',
            'timeline' => 'builder-wedding-section-timeline',
            'venue' => 'builder-wedding-section-venue',
            'gift_list' => 'builder-wedding-section-gift-list',
            'rsvp' => 'builder-wedding-section-rsvp',
            'wishes' => 'builder-wedding-section-wishes',
            'menu' => 'builder-wedding-section-menu',
        ];
    }

    /**
     * @param  array<int, string>  $blocks
     */
    public static function composeOrderedBlockSections(array $blocks, array $viewData): string
    {
        $partials = self::blockSectionPartials();
        $sectionsHtml = '';

        foreach ($blocks as $blockKey) {
            $partial = $partials[$blockKey] ?? null;
            if ($partial === null) {
                continue;
            }

            $sectionsHtml .= view('invitation.templates.partials.'.$partial, $viewData)->render()."\n\n  ";
        }

        return rtrim($sectionsHtml);
    }

    public static function replaceBetweenMarkers(
        string $html,
        string $startNeedle,
        string $endMarker,
        string $insert
    ): string {
        $start = strpos($html, $startNeedle);
        if ($start === false) {
            return $html;
        }

        $end = strpos($html, $endMarker, $start);
        if ($end === false) {
            return $html;
        }

        return substr($html, 0, $start).$insert."\n\n  ".substr($html, $end);
    }

    protected static function firstNonEmpty(mixed ...$values): ?string
    {
        foreach ($values as $value) {
            $text = trim((string) ($value ?? ''));
            if ($text !== '') {
                return $text;
            }
        }

        return null;
    }

    protected static function parseFlexibleDate(?string $value): ?Carbon
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected static function resolveMapQuery(Invitation $invitation, string $venueLocation, string $venueName): ?string
    {
        $lat = $invitation->latitude;
        $lng = $invitation->longitude;

        if ($lat !== null && $lat !== '' && $lng !== null && $lng !== '' && is_numeric($lat) && is_numeric($lng)) {
            return ((float) $lat).','.((float) $lng);
        }

        if ($venueLocation !== '') {
            return $venueLocation;
        }

        if ($venueName !== '') {
            return $venueName;
        }

        return null;
    }

    protected static function buildMapLink(?string $query): string
    {
        if ($query === null || $query === '') {
            return '#';
        }

        if (preg_match('/^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/', $query)) {
            return 'https://www.google.com/maps?q='.urlencode($query);
        }

        return 'https://www.google.com/maps/search/?api=1&query='.urlencode($query);
    }

    protected static function buildMapEmbedUrl(?string $query, string $locale): string
    {
        if ($query === null || $query === '') {
            return '';
        }

        $lang = str_starts_with($locale, 'ar') ? 'ar' : 'en';

        return 'https://www.google.com/maps?q='.urlencode($query).'&hl='.$lang.'&z=15&output=embed';
    }

    protected static function parseFlexibleTime(?string $value, ?Carbon $dateCarbon): ?Carbon
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        try {
            if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $value) && $dateCarbon) {
                return Carbon::parse($dateCarbon->format('Y-m-d').' '.$value);
            }

            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function defaultSealColorForStyle(?string $slug): string
    {
        $def = config('invitation_builder.seal_styles.'.($slug ?: 'wax_classic'), []);
        if (! is_array($def)) {
            $def = [];
        }

        $color = trim((string) ($def['default_color'] ?? ''));
        if ($color !== '' && self::normalizeSealHex($color) !== null) {
            return self::normalizeSealHex($color);
        }

        $palette = (string) ($def['palette'] ?? 'crimson');

        return config('invitation_builder.seal_palette_colors.'.$palette, '#a31830');
    }

    public static function normalizeSealHex(?string $hex): ?string
    {
        $hex = trim((string) ($hex ?? ''));
        if ($hex === '') {
            return null;
        }
        if (preg_match('/^#?([0-9A-Fa-f]{6})$/', $hex, $m)) {
            return '#'.strtolower($m[1]);
        }

        return null;
    }

    public static function resolveSealColor(?string $styleSlug, ?string $customColor): string
    {
        $normalized = self::normalizeSealHex($customColor);
        if ($normalized !== null) {
            return $normalized;
        }

        return self::defaultSealColorForStyle($styleSlug);
    }

    public static function sealInlineStyle(?string $hex): string
    {
        $hex = self::normalizeSealHex($hex);
        if ($hex === null) {
            return '';
        }

        return implode(' ', [
            '--s-mid: '.$hex.';',
            '--s-lo: color-mix(in srgb, '.$hex.' 58%, #000);',
            '--s-hi: color-mix(in srgb, '.$hex.' 38%, #fff);',
            '--s-drip: color-mix(in srgb, '.$hex.' 72%, #000);',
            '--s-ink: color-mix(in srgb, '.$hex.' 18%, #fff);',
        ]);
    }

    /** @return array{wiSealShape: string, wiSealPalette: string, wiSealRing: bool, wiSealDrip: bool, wiSealColor: string, wiSealInlineStyle: string} */
    public static function sealViewVars(?string $slug, ?string $customColor = null): array
    {
        $def = config('invitation_builder.seal_styles.'.($slug ?: 'wax_classic'), []);
        if (! is_array($def)) {
            $def = [];
        }

        $color = self::resolveSealColor($slug, $customColor);

        return [
            'wiSealShape' => (string) ($def['shape'] ?? 'wax-round'),
            'wiSealPalette' => (string) ($def['palette'] ?? 'crimson'),
            'wiSealRing' => (bool) ($def['ring'] ?? true),
            'wiSealDrip' => (bool) ($def['drip'] ?? true),
            'wiSealColor' => $color,
            'wiSealInlineStyle' => self::sealInlineStyle($color),
        ];
    }
}
