<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Constant;
use App\Services\External\Notification as PushNotificationService;
use Carbon\Carbon;
use Mpdf\Mpdf;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {


        $contacts = ContactUs::orderBy('created_at', 'desc')->paginate();
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
        ]);
    }

    public function reply(Request $request)
    {
        $contact=ContactUs::whereId($request->contact_id)->first();
        return view('pages.contact-us.reply', compact('contact'));

    }
    public function sendReply(Request $request)
    {
        $contact=ContactUs::whereId($request->contact_id)->first();
        $contact->update(['status'=>1]);
        // $data['name'] = $contact->name;
        // $data['text'] = $request->message;
        // Mail::send('mail', $data, function ($message) use ($contact) {
        //     $message->to($contact->email)->subject('Contact Us Reply');
        //     $message->from('info@modern-invitation.com', 'Modern Invitation Application');
        // });
        PushNotificationService::notify(
            'users',
            Constant::NOTIFICATIONS_TYPE['Admin'],
            [$contact->user_id],
            $contact->id,
            __('admin.contact_us_reply') . ' ' . $contact->subject,
            $request->message,
            false  // Don't use translation - send as plain text
        );

        return redirect()->route('contact.index')->with('success', 'تم إرسال الرد بنجاح');


    }

    public function destroy($id)
    {

        $contact = ContactUs::whereId($id)->first();
        $contact->delete();

        return redirect()->back()->with('success', 'Deleted');

    }

     public function contactExportPdf()
    {
        $contacts = ContactUs::orderBy('created_at', 'desc')->get();

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