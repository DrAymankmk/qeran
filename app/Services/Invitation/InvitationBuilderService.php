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
        return [
            'event_types' => config('invitation_builder.event_types', []),
            'opening_types' => config('invitation_builder.opening_types', []),
            'theme_modes' => config('invitation_builder.theme_modes', []),
            'templates' => config('invitation_builder.templates', []),
            'features' => config('invitation_builder.features', []),
            'defaults' => config('invitation_builder.defaults', []),
        ];
    }

    public function resolve(Invitation $invitation, ?int $urlTemplateOverride = null): array
    {
        $defaults = config('invitation_builder.defaults', []);
        $row = $invitation->relationLoaded('builderSetting')
            ? $invitation->builderSetting
            : $invitation->builderSetting()->first();

        $json = is_array($row?->settings) ? $row->settings : [];

        $template = $urlTemplateOverride
            ?? (int) ($row?->theme_template ?? $defaults['theme_template'] ?? 1);

        if ($template < 1 || $template > 21) {
            $template = (int) ($defaults['theme_template'] ?? 1);
        }

        return [
            'enabled' => $row !== null,
            'published' => $row?->isPublished() ?? false,
            'template' => $template,
            'event_category' => $row?->event_category ?? $defaults['event_category'] ?? 'wedding',
            'theme_mode' => $row?->theme_mode ?? $defaults['theme_mode'] ?? 'dark',
            'opening_type' => $row?->opening_type ?? $defaults['opening_type'] ?? 'envelope',
            'primary_color' => $json['primary_color'] ?? $defaults['primary_color'] ?? '#c9a962',
            'secondary_color' => $json['secondary_color'] ?? $defaults['secondary_color'] ?? '#e8b4b8',
            'background_color' => $json['background_color'] ?? $defaults['background_color'] ?? '#1a1520',
            'text_color' => $json['text_color'] ?? $defaults['text_color'] ?? '#faf6f0',
            'font_family' => $json['font_family'] ?? $defaults['font_family'] ?? 'Cairo',
            'custom_css' => $json['custom_css'] ?? '',
            'welcome_title' => $json['welcome_title'] ?? $invitation->event_name,
            'welcome_subtitle' => $json['welcome_subtitle'] ?? ($invitation->host_name ?? ''),
            'welcome_enabled' => (bool) ($json['welcome_enabled'] ?? ($row?->opening_type === 'welcome')),
            'music_enabled' => (bool) ($json['music_enabled'] ?? $defaults['music_enabled'] ?? true),
            'animated_theme' => (bool) ($json['animated_theme'] ?? $defaults['animated_theme'] ?? true),
            'video_background' => (bool) ($json['video_background'] ?? false),
            'intro_video_enabled' => (bool) ($json['intro_video_enabled'] ?? ($row?->opening_type === 'intro_video')),
            'logo_url' => $json['logo_url'] ?? null,
            'background_media_url' => $json['background_media_url'] ?? null,
        ];
    }

    /**
     * Merge unsaved form values with stored settings (for live preview).
     */
    public function resolveFromDraft(Invitation $invitation, array $data): array
    {
        $base = $this->resolve($invitation);

        $template = (int) ($data['theme_template'] ?? $base['template']);
        if ($template < 1 || $template > 21) {
            $template = $base['template'];
        }

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

        return [
            'enabled' => true,
            'published' => true,
            'template' => $template,
            'event_category' => $data['event_category'] ?? $base['event_category'],
            'theme_mode' => $data['theme_mode'] ?? $base['theme_mode'],
            'opening_type' => $openingType,
            'primary_color' => $data['primary_color'] ?? $base['primary_color'],
            'secondary_color' => $data['secondary_color'] ?? $base['secondary_color'],
            'background_color' => $data['background_color'] ?? $base['background_color'],
            'text_color' => $data['text_color'] ?? $base['text_color'],
            'font_family' => $data['font_family'] ?? $base['font_family'],
            'custom_css' => array_key_exists('custom_css', $data) ? (string) $data['custom_css'] : $base['custom_css'],
            'welcome_title' => $data['welcome_title'] ?? $base['welcome_title'],
            'welcome_subtitle' => $data['welcome_subtitle'] ?? $base['welcome_subtitle'],
            'welcome_enabled' => $welcomeEnabled,
            'music_enabled' => $bool('music_enabled'),
            'animated_theme' => $bool('animated_theme'),
            'video_background' => $bool('video_background'),
            'intro_video_enabled' => $introEnabled,
            'logo_url' => $data['logo_url'] ?? $base['logo_url'],
            'background_media_url' => $data['background_media_url'] ?? $base['background_media_url'],
        ];
    }

    public function upsert(Invitation $invitation, array $data): InvitationBuilderSetting
    {
        $settings = [
            'primary_color' => $data['primary_color'] ?? null,
            'secondary_color' => $data['secondary_color'] ?? null,
            'background_color' => $data['background_color'] ?? null,
            'text_color' => $data['text_color'] ?? null,
            'font_family' => $data['font_family'] ?? null,
            'custom_css' => $data['custom_css'] ?? null,
            'welcome_title' => $data['welcome_title'] ?? null,
            'welcome_subtitle' => $data['welcome_subtitle'] ?? null,
            'welcome_enabled' => filter_var($data['welcome_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'music_enabled' => filter_var($data['music_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'animated_theme' => filter_var($data['animated_theme'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'video_background' => filter_var($data['video_background'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'intro_video_enabled' => filter_var($data['intro_video_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'logo_url' => $data['logo_url'] ?? null,
            'background_media_url' => $data['background_media_url'] ?? null,
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

        return InvitationBuilderSetting::query()->updateOrCreate(
            ['invitation_id' => $invitation->id],
            [
                'event_category' => $data['event_category'] ?? 'wedding',
                'theme_template' => (int) ($data['theme_template'] ?? 16),
                'theme_mode' => $data['theme_mode'] ?? 'dark',
                'opening_type' => $openingType,
                'settings' => array_filter($settings, fn ($v) => $v !== null && $v !== ''),
                'published_at' => $publishedAt,
            ]
        );
    }

    /**
     * Guest user_id + invited_by for a working preview link.
     */
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

    /**
     * Resolve the guest row for the public invitation page.
     */
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
        $invitation->loadMissing('users');
        $template = (int) ($invitation->builderSetting?->theme_template ?? config('invitation_builder.defaults.theme_template', 16));
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
