<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Constant;
use App\Services\External\Notification as PushNotificationService;
use App\Services\External\UltraMessage;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Log;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // Order by: Newest (Not Yet Replied by Admin) - Under Review - Closed
        $contacts = ContactUs::orderByConversationPriority()->paginate();
        return view('pages.contact-us.index', compact('contacts'));
    }

    public function show($id)
    {
        $contact = ContactUs::whereId($id)->first();
        return response()->json([
            'id' => $contact->id,
            'name' => $contact->name,
            'email' => $contact->email,
            'country_code' => $contact->country_code,
            'phone' => $contact->phone,
            'subject' => $contact->subject,
            'message' => $contact->message,
            'created_at' => Carbon::parse($contact->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y'),
            'status' => $contact->status,
            'conversation_status' => $contact->conversation_status,
        ]);
    }

    public function reply(Request $request)
    {
        $contact = ContactUs::whereId($request->contact_id)->first();

        // Update conversation status to "Under Review" when admin opens it
        if ($contact->conversation_status == Constant::CONTACT_CONVERSATION_STATUS['New']) {
            $contact->update([
                'conversation_status' => Constant::CONTACT_CONVERSATION_STATUS['Under Review']
            ]);
        }

        return view('pages.contact-us.reply', compact('contact'));
    }

    public function sendReply(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|exists:contact_us,id',
            'message' => 'required|string|max:1000',
            'send_whatsapp' => 'nullable|boolean',
        ]);

        $contact = ContactUs::whereId($request->contact_id)->first();

        // Update status to replied
        $contact->update([
            'status' => Constant::STATUS['Active'],
            'conversation_status' => Constant::CONTACT_CONVERSATION_STATUS['Closed']
        ]);

        // Send WhatsApp message if requested
        if ($request->has('send_whatsapp') && $request->send_whatsapp) {
            try {
                $phone = $contact->country_code . $contact->phone;
                $message = "مرحباً {$contact->name} 👋\n\n";
                $message .= "رداً على استفسارك:\n";
                $message .= "{$request->message}\n\n";
                $message .= "شكراً لتواصلك معنا!";

                UltraMessage::send($phone, '', $message);
            } catch (\Exception $e) {
                Log::error('Failed to send WhatsApp reply', [
                    'contact_id' => $contact->id,
                    'error' => $e->getMessage()
                ]);
                return redirect()->route('contact.index')
                    ->with('error', 'تم حفظ الرد ولكن فشل إرسال رسالة الواتساب');
            }
        }

        // Send push notification - Contact Us category: New Message
        if ($contact->user_id) {
            PushNotificationService::notify(
                'users',
                Constant::NOTIFICATIONS_TYPE['Admin'],
                [$contact->user_id],
                $contact->id,
                __('admin.contact_us_reply') . ' ' . $contact->subject,
                $request->message,
                false,
                Constant::NOTIFICATION_CATEGORY['Contact Us'],
                Constant::NOTIFICATION_CONTACT_TYPES['New Message']
            );
        }

        return redirect()->route('contact.index')->with('success', 'تم إرسال الرد بنجاح');
    }

    /**
     * Update conversation status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'conversation_status' => 'required|in:' . implode(',', array_values(Constant::CONTACT_CONVERSATION_STATUS))
        ]);

        $contact = ContactUs::whereId($id)->first();
        $contact->update([
            'conversation_status' => $request->conversation_status
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة المحادثة بنجاح');
    }

    public function destroy($id)
    {

        $contact = ContactUs::whereId($id)->first();
        $contact->delete();

        return redirect()->back()->with('success', 'Deleted');

    }

     public function contactExportPdf()
    {
        $contacts = ContactUs::orderByConversationPriority()->get();

        // Configure mPDF with Arabic font support
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape
            'orientation' => 'L',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
            'autoLangToFont' => true, // Automatically detect and use appropriate fonts
            'autoScriptToLang' => true, // Automatically set language
            'autoVietnamese' => true,
            'autoArabic' => true, // Enable Arabic support
            'direction' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // Build HTML content
        $html = view('pages.contact-us.pdf-export', compact('contacts'))->render();

        $mpdf->WriteHTML($html);

        $filename = 'contacts_' . date('Y-m-d_His') . '.pdf';

        return $mpdf->Output($filename, 'D'); // D = Download
    }
}
