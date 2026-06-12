<?php

namespace App\Http\Controllers\Website\V1\Invitation;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationContactLog;
use App\Helpers\Constant;
use App\Models\Category;
use App\Services\Invitation\InvitationBuilderService;

class InvitationsController extends Controller
{
    public function __construct(
        protected InvitationBuilderService $invitationBuilder
    ) {}

    public function showBuilder(string $invitation_code, $user_id = null)
    {
        $invitation = Invitation::with('builderSetting')->where('code', $invitation_code)->first();

        if (! $invitation) {
            return view('invitation-error', ['message' => 'الدعوة غير موجودة أو قد تم حذفها.']);
        }

        $builderRow = $invitation->builderSetting;
        if (! $builderRow) {
            return view('invitation-error', ['message' => 'هذه الدعوة لا تستخدم منشئ الدعوات.']);
        }

        $userId = ($user_id !== null && $user_id !== '') ? (int) $user_id : null;
        $builderPreview = request()->boolean('builder')
            || request()->query('builder') === '1'
            || ! $builderRow->isPublished();

        $user = $this->invitationBuilder->builderDisplayGuest($invitation, $userId);
        $resolvedUserId = (int) $user->id;
        $resolvedInsertedBy = (int) ($user->pivot->invited_by ?? $invitation->user_id);
        $hasRealGuest = $invitation->users()->where('user_id', $resolvedUserId)->exists();

        $host_name = $invitation->host_name;
        if ($hasRealGuest && ! empty($user->pivot->host_name)) {
            $host_name = $user->pivot->host_name;
        }

        $category = Category::where('id', $invitation->category_id)->first();

        if (! $builderPreview
            && $hasRealGuest
            && $user->pivot->seen != Constant::SEEN_STATUS['accepted']
            && $user->pivot->seen != Constant::SEEN_STATUS['declined']) {
            $invitation->users()->updateExistingPivot($resolvedUserId, ['seen' => Constant::SEEN_STATUS['seen']]);

            InvitationContactLog::query()
                ->where('invitation_id', $invitation->id)
                ->where('user_id', $resolvedUserId)
                ->when($resolvedInsertedBy, fn ($query) => $query->where('invited_by', $resolvedInsertedBy))
                ->update(['seen' => Constant::SEEN_STATUS['seen']]);

            $invitation->load('users');
            $user = $invitation->users()->where('invitation_user.user_id', $resolvedUserId)->first() ?? $user;
        }

        $builderConfig = $this->invitationBuilder->resolve($invitation, 0);
        $builderView = $builderConfig['view'] ?? null;
        $template = (int) ($builderConfig['template'] ?? 0);

        $routes = [
            'accept' => route('user.invitation.accept', ['invitation_code' => $invitation->code, 'user_id' => $resolvedUserId]),
            'decline' => route('user.invitation.decline', ['invitation_code' => $invitation->code, 'user_id' => $resolvedUserId]),
        ];

        $initialView = 'envelope';
        if ($hasRealGuest && $user->pivot->seen == Constant::SEEN_STATUS['accepted']) {
            $initialView = 'success';
            $invitation->ensureQrCodeForUser($resolvedUserId);
        } elseif ($hasRealGuest && $user->pivot->seen == Constant::SEEN_STATUS['declined']) {
            $initialView = 'decline';
        }

        return view('invitation', compact(
            'invitation',
            'user',
            'routes',
            'category',
            'host_name',
            'initialView',
            'template',
            'builderConfig',
            'builderPreview',
            'builderView'
        ));
    }

    public function show($invitation_code, $user_id, $inserted_by = null, $template = 1)
    {
        $invitation = Invitation::with('builderSetting')->where('code', $invitation_code)->first();

        if (! $invitation) {
            return view('invitation-error', ['message' => 'الدعوة غير موجودة أو قد تم حذفها.']);
        }

        $userId = (int) $user_id;
        $insertedBy = ($inserted_by !== null && $inserted_by !== '') ? (int) $inserted_by : null;
        $builderPreview = request()->boolean('builder') || request()->query('builder') === '1';

        $host_name = $invitation->host_name;
        if ($insertedBy !== null && (int) $invitation->user_id !== $insertedBy) {
            $admin = $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])
                ->wherePivot('user_id', $insertedBy)
                ->first();

            if ($admin && isset($admin->pivot) && ! empty($admin->pivot->host_name)) {
                $host_name = $admin->pivot->host_name;
            }
        }

        $user = $this->invitationBuilder->resolveGuestForShow(
            $invitation,
            $userId,
            $insertedBy,
            $builderPreview
        );

        if (! $user) {
            return view('invitation-error', ['message' => 'هذه الدعوة ليست موجهة لك أو قد تم حذف دعوتك.']);
        }

        $resolvedUserId = (int) $user->id;
        $resolvedInsertedBy = (int) ($user->pivot->invited_by ?? $insertedBy ?? $invitation->user_id);

        $category = Category::where('id', $invitation->category_id)->first();

        if (! $builderPreview
            && $user->pivot->seen != Constant::SEEN_STATUS['accepted']
            && $user->pivot->seen != Constant::SEEN_STATUS['declined']) {
            $invitation->users()->updateExistingPivot($resolvedUserId, ['seen' => Constant::SEEN_STATUS['seen']]);

            InvitationContactLog::query()
                ->where('invitation_id', $invitation->id)
                ->where('user_id', $resolvedUserId)
                ->when($resolvedInsertedBy, fn ($query) => $query->where('invited_by', $resolvedInsertedBy))
                ->update(['seen' => Constant::SEEN_STATUS['seen']]);

            $invitation->load('users');
            $user = $invitation->users()->where('invitation_user.user_id', $resolvedUserId)->first() ?? $user;
        }

        $template = (int) $template;
        if ($template < 1 || $template > 21) {
            $template = 1;
        }

        $builderConfig = null;
        $builderRow = $invitation->builderSetting;
        $useBuilder = $builderRow && ($builderRow->isPublished() || $builderPreview);

        $builderView = null;
        if ($useBuilder) {
            $builderConfig = $this->invitationBuilder->resolve($invitation, $template);
            $builderView = $builderConfig['view'] ?? null;
            $template = (int) $builderConfig['template'];
            if (($builderConfig['renderer'] ?? '') !== 'builder-wedding' && ($template < 1 || $template > 21)) {
                $template = 1;
            }
        }

        $routes = [
            'accept' => route('user.invitation.accept', ['invitation_code' => $invitation->code, 'user_id' => $resolvedUserId]),
            'decline' => route('user.invitation.decline', ['invitation_code' => $invitation->code, 'user_id' => $resolvedUserId]),
        ];

        $initialView = 'envelope';
        if ($user->pivot->seen == Constant::SEEN_STATUS['accepted']) {
            $initialView = 'success';
            $invitation->ensureQrCodeForUser($resolvedUserId);
        } elseif ($user->pivot->seen == Constant::SEEN_STATUS['declined']) {
            $initialView = 'decline';
        }

        return view('invitation', compact(
            'invitation',
            'user',
            'routes',
            'category',
            'host_name',
            'initialView',
            'template',
            'builderConfig',
            'builderPreview',
            'builderView'
        ));
    }

    public function accept($invitation_code, $user_id)
    {
        try {
            $invitation = Invitation::where('code', $invitation_code)->first();

            if (! $invitation) {
                return response()->json(['success' => false, 'message' => 'الدعوة غير موجودة'], 404);
            }

            $user = $invitation->users()->where('user_id', $user_id)->first();

            if (! $user) {
                return response()->json(['success' => false, 'message' => 'المستخدم غير موجود'], 404);
            }

            if ($user->pivot->seen == Constant::SEEN_STATUS['declined']) {
                return response()->json(['success' => false, 'message' => 'تم رفض الدعوة بالفعل'], 404);
            }

            $invitation->users()->updateExistingPivot($user_id, ['seen' => Constant::SEEN_STATUS['accepted']]);
            $invitation->ensureQrCodeForUser((int) $user_id);
            $this->syncContactLogAcceptance($invitation, (int) $user_id, Constant::ACCEPTANCE_STATUS['accepted']);

            $invitation->load('users');
            $updatedUser = $invitation->users()->where('user_id', $user_id)->first();

            return response()->json([
                'success' => true,
                'message' => 'تم قبول الدعوة بنجاح',
                'status' => 'accepted',
                'user_id' => $user_id,
                'user_seen' => $updatedUser->pivot->seen,
                'qr_url' => $invitation->qr($invitation->id, (int) $user_id),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء قبول الدعوة: '.$e->getMessage()], 500);
        }
    }

    public function decline($invitation_code, $user_id)
    {
        try {
            $invitation = Invitation::where('code', $invitation_code)->first();

            if (! $invitation) {
                return response()->json(['success' => false, 'message' => 'الدعوة غير موجودة'], 404);
            }

            $user = $invitation->users()->where('user_id', $user_id)->first();

            if (! $user) {
                return response()->json(['success' => false, 'message' => 'المستخدم غير موجود'], 404);
            }

            if ($user->pivot->seen == Constant::SEEN_STATUS['accepted']) {
                return response()->json(['success' => false, 'message' => 'تم قبول الدعوة بالفعل'], 404);
            }

            $invitation->users()->updateExistingPivot($user_id, ['seen' => Constant::SEEN_STATUS['declined']]);
            $this->syncContactLogAcceptance($invitation, (int) $user_id, Constant::ACCEPTANCE_STATUS['declined']);

            $invitation->load('users');
            $updatedUser = $invitation->users()->where('user_id', $user_id)->first();

            return response()->json([
                'success' => true,
                'message' => 'تم رفض الدعوة',
                'status' => 'declined',
                'user_seen' => $updatedUser->pivot->seen,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء رفض الدعوة: '.$e->getMessage()], 500);
        }
    }

    protected function syncContactLogAcceptance(Invitation $invitation, int $userId, int $acceptanceStatus): void
    {
        $guest = $invitation->users()->where('user_id', $userId)->first();
        $invitedBy = $guest?->pivot?->invited_by;

        $seen = $acceptanceStatus === Constant::ACCEPTANCE_STATUS['accepted']
            ? Constant::SEEN_STATUS['accepted']
            : Constant::SEEN_STATUS['declined'];

        $query = InvitationContactLog::query()
            ->where('invitation_id', $invitation->id)
            ->where('user_id', $userId);

        if ($invitedBy) {
            $query->where('invited_by', $invitedBy);
        }

        $query->update([
            'acceptance_status' => $acceptanceStatus,
            'seen' => $seen,
        ]);
    }
}
