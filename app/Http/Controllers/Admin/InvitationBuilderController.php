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
        $invitation->load('builderSetting');
        $catalog = $this->builder->catalog();
        $config = $this->builder->resolve($invitation);
        $previewUrl = $this->builder->previewUrl($invitation);
        $previewPostUrl = route('admin.invitation-builder.preview', $invitation);

        return view('admin.invitation-builder.edit', compact(
            'invitation',
            'catalog',
            'config',
            'previewUrl',
            'previewPostUrl'
        ));
    }

    public function preview(InvitationBuilderPreviewRequest $request, Invitation $invitation)
    {
        $builderConfig = $this->builder->resolveFromDraft($invitation, array_merge(
            $request->validated(),
            ['blocks' => $request->input('blocks', [])]
        ));
        $template = (int) $builderConfig['template'];
        $host_name = $invitation->host_name;

        if ($template < 1 || $template > 21) {
            $template = 1;
        }

        $view = 'invitation.templates.template'.$template;
        if (! view()->exists($view)) {
            $template = 1;
            $view = 'invitation.templates.template1';
        }

        return response()
            ->view('admin.invitation-builder.preview-frame', compact(
                'invitation',
                'builderConfig',
                'template',
                'host_name',
                'view'
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
