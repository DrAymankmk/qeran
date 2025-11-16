<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Constant;
use App\Services\External\Notification as PushNotificationService;


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
}
