<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InvitationBuilderPreviewRequest;
use App\Http\Requests\Admin\InvitationBuilderRequest;
use App\Models\Invitation;
use App\Services\Invitation\InvitationBuilderService;

class InvitationBuilderController extends Controller
{
    public function __construct(
        protected InvitationBuilderService $builder
    ) {}

    public function edit(Invitation $invitation)
    {
        $invitation->load(['builderSetting', 'hubFiles']);
        $catalog = $this->builder->catalog();
        $config = $this->builder->resolve($invitation);
        $envelopeImageChoices = $this->builder->envelopeImageChoices($invitation);
        $previewPostUrl = route('admin.invitation-builder.preview', $invitation);

        $envelopeImageRef = $config['envelope_image_ref'] ?? '';
        if ($envelopeImageRef === '' && ! empty($config['envelope_image_url'])) {
            $envelopeImageRef = $this->builder->guessEnvelopeImageRef($config['envelope_image_url'], $invitation);
        }

        return view('admin.invitation-builder.edit', compact(
            'invitation',
            'catalog',
            'config',
            'envelopeImageChoices',
            'envelopeImageRef',
            'previewPostUrl'
        ));
    }

    public function preview(InvitationBuilderPreviewRequest $request, Invitation $invitation)
    {
        $draft = array_merge(
            $request->validated(),
            ['blocks' => $request->input('blocks', [])]
        );

        $this->builder->syncInvitationPartyFields($invitation, $draft, persist: false);

        $builderConfig = $this->builder->resolveFromDraft($invitation, $draft);
        $host_name = $invitation->host_name;
        $category = \App\Models\Category::find($invitation->category_id);
        $user = new \App\Models\User(['name' => __('admin.invitation-builder-preview-guest')]);
        $user->id = $invitation->user_id;
        $invitation->ensureQrCodeForUser((int) $user->id);
        $routes = ['accept' => '#', 'decline' => '#'];
        $initialView = 'envelope';
        $useBuilderWedding = ($builderConfig['renderer'] ?? '') === 'builder-wedding';

        $view = $builderConfig['view'] ?? $this->builder->resolveViewName($builderConfig['theme_slug'] ?? null);
        $template = (int) ($builderConfig['template'] ?? 0);

        return response()
            ->view('admin.invitation-builder.preview-frame', compact(
                'invitation',
                'builderConfig',
                'template',
                'host_name',
                'view',
                'category',
                'user',
                'routes',
                'initialView',
                'useBuilderWedding'
            ))
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }

    public function update(InvitationBuilderRequest $request, Invitation $invitation)
    {
        $this->builder->upsert($invitation, $request->validated());

        return redirect()
            ->route('admin.invitation-builder.edit', $invitation)
            ->with('success', __('admin.invitation-builder-saved'));
    }
}
