<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InvitationBuilderRequest;
use App\Models\Invitation;
use App\Services\Invitation\InvitationBuilderService;
use Illuminate\Http\Request;

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

        return view('admin.invitation-builder.edit', compact('invitation', 'catalog', 'config', 'previewUrl'));
    }

    public function update(InvitationBuilderRequest $request, Invitation $invitation)
    {
        $this->builder->upsert($invitation, $request->validated());

        return redirect()
            ->route('admin.invitation-builder.edit', $invitation)
            ->with('success', __('admin.invitation-builder-saved'));
    }
}
