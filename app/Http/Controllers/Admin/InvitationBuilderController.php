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

    public function previewShow(Invitation $invitation)
    {
        $invitation->load(['builderSetting', 'hubFiles']);

        return $this->standalonePreviewResponse(
            $this->compilePreviewContext($invitation, $this->builder->resolve($invitation))
        );
    }

    public function preview(InvitationBuilderPreviewRequest $request, Invitation $invitation)
    {
        $draft = $request->validated();
        $draft['blocks'] = $request->input('blocks', $draft['blocks'] ?? []);

        $this->builder->syncInvitationPartyFields($invitation, $draft, persist: false);

        $context = $this->compilePreviewContext(
            $invitation,
            $this->builder->resolveFromDraft($invitation, $draft)
        );

        if ($request->boolean('preview_standalone')) {
            return $this->standalonePreviewResponse($context);
        }

        return $this->embedPreviewResponse($context);
    }

    /**
     * @return array<string, mixed>
     */
    protected function compilePreviewContext(Invitation $invitation, array $builderConfig): array
    {
        $host_name = $invitation->host_name;
        $category = \App\Models\Category::find($invitation->category_id);
        $user = new \App\Models\User(['name' => __('admin.invitation-builder-preview-guest')]);
        $user->id = $invitation->user_id;
        $invitation->ensureQrCodeForUser((int) $user->id);
        $useBuilderWedding = ($builderConfig['renderer'] ?? '') === 'builder-wedding';
        $view = $builderConfig['view'] ?? $this->builder->resolveViewName($builderConfig['theme_slug'] ?? null);
        $template = (int) ($builderConfig['template'] ?? 0);

        return [
            'invitation' => $invitation,
            'builderConfig' => $builderConfig,
            'template' => $template,
            'host_name' => $host_name,
            'view' => $view,
            'category' => $category,
            'user' => $user,
            'routes' => ['accept' => '#', 'decline' => '#'],
            'initialView' => 'envelope',
            'useBuilderWedding' => $useBuilderWedding,
            'isBuilderPreview' => true,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function embedPreviewResponse(array $context)
    {
        return response()
            ->view('admin.invitation-builder.preview-frame', $context)
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function standalonePreviewResponse(array $context)
    {
        $embedHtml = view('admin.invitation-builder.preview-frame', $context)->render();

        return response()->view('admin.invitation-builder.preview-standalone', [
            'invitation' => $context['invitation'],
            'embedHtml' => $embedHtml,
            'backUrl' => route('admin.invitation-builder.edit', $context['invitation']),
        ]);
    }

    public function update(InvitationBuilderRequest $request, Invitation $invitation)
    {
        $this->builder->upsert($invitation, $request->validated());

        return redirect()
            ->route('admin.invitation-builder.edit', $invitation)
            ->with('success', __('admin.invitation-builder-saved'));
    }
}
