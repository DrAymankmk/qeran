<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Setting\SetContactUsRequest;
use App\Models\ContactUs;
use App\Services\RespondActive;
use App\Services\External\BaileysWhatsApp;
use App\Services\External\Notification;
use Illuminate\Support\Facades\Log;
use App\Traits\SendsNotificationAndEmail;
class SetContactUs extends Controller
{
    use SendsNotificationAndEmail;
    public function __invoke(SetContactUsRequest $request)
    {
        $contact = ContactUs::create($request->validated() + [
            'user_id' => auth('sanctum')->id(),
            'conversation_status' => Constant::CONTACT_CONVERSATION_STATUS['New'],
            'status' => Constant::STATUS['Not active'], // Not replied yet
        ]);

        try {
            $this->sendAdminNotification(
                notificationKey: 'new_message_contact_us',
                targetId: $contact->id,
                params: [
                    'contact_id' => $contact->id,
                    'name' => $contact->name,
                    'contact_type' => 'Contact',
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'status' => 'New Message',

                ],
                category: Constant::NOTIFICATION_CATEGORY['Contact Us'] ?? 1,
                notificationType: Constant::NOTIFICATION_CONTACT_TYPES['New Message'] ?? 1,
                emailSubject: 'New Message / Contact Us - '.($contact->name),
                emailView: 'emails.contact_us.new_message',
                emailTo: env('MAIL_TO_ADDRESS'),
                emailData: [
                    'contact' => $contact,
                    'contact_type' => 'Contact',
                    'status' => 'New Message',
                    'subject' => $contact->subject,
                    'message' => $contact->message,


                ]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send new message notification: '.$e->getMessage(), [
                'contact_id' => $contact->id,
                'error' => $e->getTraceAsString(),
            ]);
        }

        // Send immediate WhatsApp reply
        try {
            $phone = $request->country_code . $request->phone;
            $message = "شكراً لتواصلك معنا! 📱\n\n";
            $message .= "لقد استلمنا رسالتك وسنقوم بالرد عليك قريباً.\n\n";
            $message .= "رقم الطلب: #{$contact->id}\n";
            $message .= "الموضوع: {$request->subject}";

            // TwilioWhatsApp::send sends message directly
            BaileysWhatsApp::send($phone, $message);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Failed to send WhatsApp reply for contact us', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage()
            ]);
        }

        return RespondActive::success('Message Sent, we will contact you soon.');
    }
}