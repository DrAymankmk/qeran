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

    public function showBuilderContact(string $invitation_code, $contact_log_id)
    {
        return $this->renderContactInvitation($invitation_code, (int) $contact_log_id, builder: true);
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

    public function showContact(string $invitation_code, $contact_log_id, $template = 1)
    {
        return $this->renderContactInvitation($invitation_code, (int) $contact_log_id, builder: false, template: (int) $template);
    }

    public function accept($invitation_code, $user_id)
    {
        try {
            $invitation = Invitation::where('code', $invitation_code)->first();

            if (! $invitation) {
                return response()->json(['success' => false, 'message' => 'الدعوة غير موجودة'], 404);
            }

            $userId = (int) $user_id;
            $user = $invitation->users()->where('user_id', $userId)->first();

            if (! $user) {
                return response()->json(['success' => false, 'message' => 'المستخدم غير موجود'], 404);
            }

            if ($user->pivot->seen == Constant::SEEN_STATUS['declined']) {
                return response()->json(['success' => false, 'message' => 'تم رفض الدعوة بالفعل'], 404);
            }

            $invitation->users()->updateExistingPivot($userId, ['seen' => Constant::SEEN_STATUS['accepted']]);
            $invitation->ensureQrCodeForUser($userId);

            $invitation->load('users');
            $updatedUser = $invitation->users()->where('user_id', $userId)->first();

            return response()->json([
                'success' => true,
                'message' => 'تم قبول الدعوة بنجاح',
                'status' => 'accepted',
                'user_id' => $userId,
                'user_seen' => $updatedUser->pivot->seen,
                'qr_url' => $invitation->qr($invitation->id, $userId),
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

            $userId = (int) $user_id;
            $user = $invitation->users()->where('user_id', $userId)->first();

            if (! $user) {
                return response()->json(['success' => false, 'message' => 'المستخدم غير موجود'], 404);
            }

            if ($user->pivot->seen == Constant::SEEN_STATUS['accepted']) {
                return response()->json(['success' => false, 'message' => 'تم قبول الدعوة بالفعل'], 404);
            }

            $invitation->users()->updateExistingPivot($userId, ['seen' => Constant::SEEN_STATUS['declined']]);

            $invitation->load('users');
            $updatedUser = $invitation->users()->where('user_id', $userId)->first();

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

    public function acceptContact($invitation_code, $contact_log_id)
    {
        try {
            $contactLog = $this->resolveContactLog($invitation_code, (int) $contact_log_id);

            if (! $contactLog) {
                return response()->json(['success' => false, 'message' => 'جهة الاتصال غير موجودة'], 404);
            }

            $invitation = $contactLog->invitation;

            if ((int) $contactLog->acceptance_status === Constant::ACCEPTANCE_STATUS['declined']) {
                return response()->json(['success' => false, 'message' => 'تم رفض الدعوة بالفعل'], 404);
            }

            $contactLog->update([
                'acceptance_status' => Constant::ACCEPTANCE_STATUS['accepted'],
                'seen' => Constant::SEEN_STATUS['accepted'],
            ]);

            $invitation->ensureQrCodeForContact((int) $contactLog->id);

            return response()->json([
                'success' => true,
                'message' => 'تم قبول الدعوة بنجاح',
                'status' => 'accepted',
                'contact_log_id' => (int) $contactLog->id,
                'acceptance_status' => Constant::ACCEPTANCE_STATUS['accepted'],
                'qr_url' => $invitation->qrForContact((int) $contactLog->id),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء قبول الدعوة: '.$e->getMessage()], 500);
        }
    }

    public function declineContact($invitation_code, $contact_log_id)
    {
        try {
            $contactLog = $this->resolveContactLog($invitation_code, (int) $contact_log_id);

            if (! $contactLog) {
                return response()->json(['success' => false, 'message' => 'جهة الاتصال غير موجودة'], 404);
            }

            if ((int) $contactLog->acceptance_status === Constant::ACCEPTANCE_STATUS['accepted']) {
                return response()->json(['success' => false, 'message' => 'تم قبول الدعوة بالفعل'], 404);
            }

            $contactLog->update([
                'acceptance_status' => Constant::ACCEPTANCE_STATUS['declined'],
                'seen' => Constant::SEEN_STATUS['declined'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم رفض الدعوة',
                'status' => 'declined',
                'contact_log_id' => (int) $contactLog->id,
                'acceptance_status' => Constant::ACCEPTANCE_STATUS['declined'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء رفض الدعوة: '.$e->getMessage()], 500);
        }
    }

    protected function renderContactInvitation(
        string $invitationCode,
        int $contactLogId,
        bool $builder,
        int $template = 1
    ) {
        $contactLog = $this->resolveContactLog($invitationCode, $contactLogId);

        if (! $contactLog) {
            return view('invitation-error', ['message' => 'الدعوة غير موجودة أو جهة الاتصال غير صالحة.']);
        }

        $invitation = $contactLog->invitation;
        $builderRow = $invitation->builderSetting;

        if ($builder && ! $builderRow) {
            return view('invitation-error', ['message' => 'هذه الدعوة لا تستخدم منشئ الدعوات.']);
        }

        $builderPreview = request()->boolean('builder')
            || request()->query('builder') === '1'
            || ($builder && $builderRow && ! $builderRow->isPublished());

        if (! $builderPreview && ! $this->contactHasResponded($contactLog)) {
            $contactLog->update(['seen' => Constant::SEEN_STATUS['seen']]);
            $contactLog->refresh();
        }

        $user = $this->invitationBuilder->guestDisplayFromContactLog($contactLog);
        $host_name = $invitation->host_name;
        $category = Category::where('id', $invitation->category_id)->first();

        $builderConfig = null;
        $builderView = null;

        if ($builder || ($builderRow && ($builderRow->isPublished() || $builderPreview))) {
            $builderConfig = $this->invitationBuilder->resolve($invitation, $builder ? 0 : $template);
            $builderView = $builderConfig['view'] ?? null;
            $template = (int) ($builderConfig['template'] ?? ($builder ? 0 : $template));
            if (($builderConfig['renderer'] ?? '') !== 'builder-wedding' && ($template < 1 || $template > 21)) {
                $template = 1;
            }
        } elseif ($template < 1 || $template > 21) {
            $template = 1;
        }

        $routes = [
            'accept' => route('user.invitation.contact.accept', [
                'invitation_code' => $invitation->code,
                'contact_log_id' => $contactLog->id,
            ]),
            'decline' => route('user.invitation.contact.decline', [
                'invitation_code' => $invitation->code,
                'contact_log_id' => $contactLog->id,
            ]),
        ];

        $initialView = $this->resolveContactInitialView($invitation, $contactLog);

        return view('invitation', compact(
            'invitation',
            'user',
            'contactLog',
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

    protected function resolveContactLog(string $invitationCode, int $contactLogId): ?InvitationContactLog
    {
        $invitation = Invitation::where('code', $invitationCode)->first();

        if (! $invitation) {
            return null;
        }

        return InvitationContactLog::query()
            ->with('invitation')
            ->where('id', $contactLogId)
            ->where('invitation_id', $invitation->id)
            ->first();
    }

    protected function contactHasResponded(InvitationContactLog $contactLog): bool
    {
        if ($contactLog->acceptance_status === null) {
            return false;
        }

        $status = (int) $contactLog->acceptance_status;

        return $status === Constant::ACCEPTANCE_STATUS['accepted']
            || $status === Constant::ACCEPTANCE_STATUS['declined'];
    }

    protected function resolveContactInitialView(Invitation $invitation, InvitationContactLog $contactLog): string
    {
        if ($contactLog->acceptance_status === null) {
            return 'envelope';
        }

        if ((int) $contactLog->acceptance_status === Constant::ACCEPTANCE_STATUS['accepted']) {
            $invitation->ensureQrCodeForContact((int) $contactLog->id);

            return 'success';
        }

        if ((int) $contactLog->acceptance_status === Constant::ACCEPTANCE_STATUS['declined']) {
            return 'decline';
        }

        return 'envelope';
    }
}
