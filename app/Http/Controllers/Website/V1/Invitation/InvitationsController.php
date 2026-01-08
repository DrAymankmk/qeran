<?php

namespace App\Http\Controllers\Website\V1\Invitation;

use App\Http\Controllers\Controller;
use App\Http\Resources\Invitations\InvitationResource;
use App\Models\Invitation;
use App\Services\RespondActive;
use Illuminate\Http\Request;
use App\Helpers\Constant;
use App\Models\Category;

class InvitationsController extends Controller
{


    public function show($invitation_code,$user_id , $inserted_by = null, $template = 1)
    {
        $invitation=Invitation::where('code',$invitation_code)->first();

        // Determine host name based on who inserted the user (owner vs admin)
        $host_name = $invitation->host_name;
        if ($inserted_by !== null && (int) $invitation->user_id !== (int) $inserted_by) {
            // Look up the admin who invited the user and read host_name from pivot
            $admin = $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])
                ->wherePivot('user_id', $inserted_by)
                ->first();

            if ($admin && isset($admin->pivot) && !empty($admin->pivot->host_name)) {
                $host_name = $admin->pivot->host_name;
            }
        }

        if (!$invitation) {
            return view('invitation-error', ['message' => 'الدعوة غير موجودة أو قد تم حذفها.']);
        }

        $user= $invitation->users()->where('user_id', $user_id)->where("invited_by", $inserted_by)->first();
        $category=Category::where('id',$invitation->category_id)->first();

        if (!$user) {
            return view('invitation-error', ['message' => 'هذه الدعوة ليست موجهة لك أو قد تم حذف دعوتك.']);
        }

        // Update seen status if not already accepted/declined
        if ($user->pivot->seen != Constant::SEEN_STATUS['accepted'] && $user->pivot->seen != Constant::SEEN_STATUS['declined']) {
            $invitation->users()->updateExistingPivot($user_id, ['seen' => Constant::SEEN_STATUS['seen']]);
            // Refresh the relationship to get updated pivot data
            $invitation->load('users');
            $user = $invitation->users()->where('user_id', $user_id)->first();
        }

        // Validate template number (default to 1 if invalid)
        $template = (int) $template;
        if ($template < 1 || $template > 20) {
            $template = 1;
        }

        // Pass routes for the accept/decline actions
        $routes = [
            'accept' => route('user.invitation.accept', ['invitation_code' => $invitation->code, 'user_id' => $user_id]),
            'decline' => route('user.invitation.decline', ['invitation_code' => $invitation->code, 'user_id' => $user_id])
        ];

        // Determine initial view based on user status
        $initialView = 'envelope';
        if ($user->pivot->seen == Constant::SEEN_STATUS['accepted']) {
            $initialView = 'success';
        } elseif ($user->pivot->seen == Constant::SEEN_STATUS['declined']) {
            $initialView = 'decline';
        }

        return view('invitation', compact('invitation', 'user', 'routes', 'category', 'host_name', 'initialView', 'template'));
    }

    public function accept($invitation_code, $user_id)
    {
        try {
            $invitation = Invitation::where('code', $invitation_code)->first();

            if (!$invitation) {
                return response()->json(['success' => false, 'message' => 'الدعوة غير موجودة'], 404);
            }

            // Check if user exists in the pivot table
            $user = $invitation->users()->where('user_id', $user_id)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'المستخدم غير موجود'], 404);
            }

            if($user->pivot->seen == Constant::SEEN_STATUS['declined']){
                return response()->json(['success' => false, 'message' => 'تم رفض الدعوة بالفعل'], 404);
            }

            // Update the pivot table
            $invitation->users()->updateExistingPivot($user_id, ['seen' => Constant::SEEN_STATUS['accepted']]);

            // Refresh the relationship to get updated pivot data
            $invitation->load('users');
            $updatedUser = $invitation->users()->where('user_id', $user_id)->first();


            return response()->json([
                'success' => true,
                'message' => 'تم قبول الدعوة بنجاح',
                'status' => 'accepted',
                'user_id' => $user_id,
                'user_seen' => $updatedUser->pivot->seen
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء قبول الدعوة: ' . $e->getMessage()], 500);
        }
    }

    public function decline($invitation_code, $user_id)
    {
        try {
            $invitation = Invitation::where('code', $invitation_code)->first();

            if (!$invitation) {
                return response()->json(['success' => false, 'message' => 'الدعوة غير موجودة'], 404);
            }

            // Check if user exists in the pivot table
            $user = $invitation->users()->where('user_id', $user_id)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'المستخدم غير موجود'], 404);
            }

            if($user->pivot->seen == Constant::SEEN_STATUS['accepted']){
                return response()->json(['success' => false, 'message' => 'تم قبول الدعوة بالفعل'], 404);
            }

            // Update the pivot table
            $invitation->users()->updateExistingPivot($user_id, ['seen' => Constant::SEEN_STATUS['declined']]);

            // Refresh the relationship to get updated pivot data
            $invitation->load('users');
            $updatedUser = $invitation->users()->where('user_id', $user_id)->first();

            return response()->json([
                'success' => true,
                'message' => 'تم رفض الدعوة',
                'status' => 'declined',
                'user_seen' => $updatedUser->pivot->seen
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء رفض الدعوة: ' . $e->getMessage()], 500);
        }
    }
}
