<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Services\External\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mpdf\Mpdf;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
//        abort_if(Gate::denies('all_users'), 403);


        $users = User::where('account_type',1)->orderBy('created_at', 'desc')->get();
        return view('pages.users.index', compact('users'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::whereId($id)->with('myInvitations')->first();

        return view('pages.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
//        abort_if(Gate::denies('edit_users'), 403);

        $user = User::whereId($id)->first();
        return view('pages.users.edit', compact('user'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
//        abort_if(Gate::denies('delete_users'), 403);

        $user = User::whereId($id)->first();
        if ($user->hubFiles()->exists()) {
            deleteImage($user->image(), $user->hubFiles());
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Deleted');

    }

    public function status(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:1,2,3'
        ]);

        $newStatus = (int) $request->status;

        $user->update([
            'verified' => $newStatus
        ]);

        if ($newStatus == Constant::USER_STATUS['Suspended']) {
            // User category: Account Banned or Unbanned
            Notification::notify('users',
                Constant::NOTIFICATIONS_TYPE['Admin'],
                [$user->id],
                $user->id,
                'Modern Invitation',
                'You are blocked by admin!',
                false,
                Constant::NOTIFICATION_CATEGORY['User'],
                Constant::NOTIFICATION_USER_TYPES['Account Banned or Unbanned']);
        } elseif ($newStatus == Constant::USER_STATUS['Verified']) {
            // User category: Account Activated
            Notification::notify('users',
                Constant::NOTIFICATIONS_TYPE['Admin'],
                [$user->id],
                $user->id,
                'Modern Invitation',
                'Your account has been verified!',
                false,
                Constant::NOTIFICATION_CATEGORY['User'],
                Constant::NOTIFICATION_USER_TYPES['Account Activated']);
        }

        return response()->json([
            'success' => true,
            'message' => __('admin.updated-successfully'),
            'verified' => $newStatus
        ]);
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::whereId($id)->first();
        $data = $request->validated();

        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // Handle image upload (support both 'img' and 'image' field names)
        $imageFile = $request->hasFile('img') ? $request->file('img') : ($request->hasFile('image') ? $request->file('image') : null);

        if ($imageFile) {
            // Delete old image if exists
            if ($user->hubFiles) {
                deleteImage($user->hubFiles->get_folder_file(), $user->hubFiles);
            }

            // Store new image
            storeImage([
                'value' => $imageFile,
                'folderName' => Constant::USER_IMAGE_FOLDER_NAME,
                'file_key' => Constant::FILE_KEY['Main'],
                'file_type' => Constant::FILE_TYPE['Image'],
                'model' => $user,
                'saveInDatabase' => true
            ]);
        }

        return redirect()->route('users.index')->with('success', __('admin.updated-successfully'));
    }

      public function usersExportPdf(){
         $users = User::whereNull('deleted_at')
         ->where('account_type',1)
         ->orderBy('created_at', 'desc')->get();


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
        $html = view('pages.users.pdf-export', compact('users'))->render();

        $mpdf->WriteHTML($html);

        $filename = 'users_' . date('Y-m-d_His') . '.pdf';

        return $mpdf->Output($filename, 'D'); // D = Download
    }

}
