<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Setting\SetContactUsRequest;
use App\Models\ContactUs;
use App\Services\RespondActive;
use App\Services\External\UltraMessage;
use App\Services\External\Notification;
use Illuminate\Support\Facades\Log;

class SetContactUs extends Controller
{
    public function __invoke(SetContactUsRequest $request)
    {
        $contact = ContactUs::create($request->validated() + [
            'user_id' => auth('sanctum')->id(),
            'conversation_status' => Constant::CONTACT_CONVERSATION_STATUS['New'],
            'status' => Constant::STATUS['Not active'], // Not replied yet
        ]);

        // Send immediate WhatsApp reply
        try {
            $phone = $request->country_code . $request->phone;
            $message = "شكراً لتواصلك معنا! 📱\n\n";
            $message .= "لقد استلمنا رسالتك وسنقوم بالرد عليك قريباً.\n\n";
            $message .= "رقم الطلب: #{$contact->id}\n";
            $message .= "الموضوع: {$request->subject}";

            // UltraMessage::send appends activation code, so we pass empty string and message
            UltraMessage::send($phone, '', $message);
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
