<?php

namespace App\Http\Controllers;

use App\Traits\SendsNotificationAndEmail;
use App\Helpers\Constant;
use App\Services\RespondActive;
use Illuminate\Http\Request;

/**
 * Example Controller showing how to use the SendsNotificationAndEmail trait
 * 
 * This is an example file. You can use the trait in any controller.
 */
class ExampleNotificationController extends Controller
{
    use SendsNotificationAndEmail;

    /**
     * Example: Send notification with email
     */
    public function sendNotificationWithEmail(Request $request)
    {
        $userId = $request->input('user_id');
        $invitationId = $request->input('invitation_id');

        // Send notification and email sequentially
        $this->sendNotificationAndEmail(
            userType: 'users',
            userIds: [$userId],
            notifyType: Constant::NOTIFICATIONS_TYPE['Invitations'],
            targetId: $invitationId,
            notificationKey: 'invitation_created',
            params: ['invitation_id' => $invitationId],
            useTranslation: true,
            category: Constant::NOTIFICATION_CATEGORY['Order'] ?? null,
            notificationType: 'new_invitation',
            emailView: 'emails.notification', // Optional: custom email view
            emailData: ['invitation_id' => $invitationId], // Optional: additional email data
            emailSubject: 'New Invitation Created', // Optional: custom email subject
            emailTo: null // Optional: defaults to user email
        );

        return RespondActive::success('Notification and email sent successfully');
    }

    /**
     * Example: Send notification only (no email)
     */
    public function sendNotificationOnly(Request $request)
    {
        $userId = $request->input('user_id');
        $invitationId = $request->input('invitation_id');

        // Send notification without email (don't provide emailView or emailSubject)
        $this->sendNotificationAndEmail(
            userType: 'users',
            userIds: [$userId],
            notifyType: Constant::NOTIFICATIONS_TYPE['Invitations'],
            targetId: $invitationId,
            notificationKey: 'invitation_updated',
            params: ['invitation_id' => $invitationId],
            useTranslation: true,
            category: Constant::NOTIFICATION_CATEGORY['Order'] ?? null,
            notificationType: 'invitation_updated'
        );

        return RespondActive::success('Notification sent successfully');
    }

    /**
     * Example: Send to multiple users
     */
    public function sendToMultipleUsers(Request $request)
    {
        $userIds = $request->input('user_ids', []);
        $invitationId = $request->input('invitation_id');

        $this->sendNotificationAndEmail(
            userType: 'users',
            userIds: $userIds,
            notifyType: Constant::NOTIFICATIONS_TYPE['Invitations'],
            targetId: $invitationId,
            notificationKey: 'invitation_shared',
            params: ['invitation_id' => $invitationId],
            useTranslation: true,
            category: Constant::NOTIFICATION_CATEGORY['Order'] ?? null,
            emailView: 'emails.notification',
            emailSubject: 'Invitation Shared'
        );

        return RespondActive::success('Notifications sent to multiple users');
    }
}















































