<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsPage;
use App\Models\ContactUs;
use App\Helpers\Constant;
use App\Traits\SendsNotificationAndEmail;
use App\Services\External\UltraMessage;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    use SendsNotificationAndEmail;

    public function index()
    {
        $contactPage = CmsPage::where('slug', 'contact')->where('is_active', true)
        ->with([
            'activeSections.items' => function($query) {
                $query->where('is_active', true)
                    ->with(['translations', 'media', 'links'])
                    ->orderBy('order');
            },
        ])
        ->firstOrFail();
        return view('frontend.pages.contact.index', compact('contactPage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'email' => 'nullable|email|max:100',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:500',
            'message' => 'required|string|max:5000',
        ]);

        // Extract country code from phone if provided, otherwise default
        $countryCode = $request->input('country_code', '966'); // Default to Saudi Arabia
        $phone = preg_replace('/[^0-9]/', '', $validated['phone']); // Remove non-numeric characters

        $contact = ContactUs::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'country_code' => $countryCode,
            'phone' => $phone,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'user_id' => null, // null if not logged in
            'contact_type' => Constant::CONTACT_US_TYPE['Contact'],
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
                emailSubject: 'New Message / Contact Us - ' . ($contact->name),
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
            Log::error('Failed to send new message notification: ' . $e->getMessage(), [
                'contact_id' => $contact->id,
                'error' => $e->getTraceAsString(),
            ]);
        }

        // Send immediate WhatsApp reply if phone is available
        if ($phone) {
            try {
                $fullPhone = $countryCode . $phone;
                $message = "شكراً لتواصلك معنا! 📱\n\n";
                $message .= "لقد استلمنا رسالتك وسنقوم بالرد عليك قريباً.\n\n";
                $message .= "رقم الطلب: #{$contact->id}\n";
                $message .= "الموضوع: {$validated['subject']}";

                UltraMessage::send($fullPhone, '', $message);
            } catch (\Exception $e) {
                Log::error('Failed to send WhatsApp reply for contact us', [
                    'contact_id' => $contact->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('frontend.contact_success_message', [], app()->getLocale())
            ]);
        }

        return back()->with('success', __('frontend.contact_success_message', [], app()->getLocale()));
    }
}