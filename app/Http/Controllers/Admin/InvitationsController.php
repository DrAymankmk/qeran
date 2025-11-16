<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Requests\Admin\InvitationRequest;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationPackage;
use App\Services\External\Notification;
use Illuminate\Http\Request;

class InvitationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        abort_if(Gate::denies('all_categories'), 403);
        $invitations=Invitation::orderBy('created_at','desc')->whereNotNull('user_id')->paginate();
//        return $invitations[0]->designImage();
        return view('pages.invitation.index', compact('invitations'));

    }

    public function requests(Request $request)
    {
        $invitations=Invitation::where('invitation_type',$request->invitation_type)
            ->whereNotNull('user_id')
            ->whereIn('status',[Constant::INVITATION_STATUS['Pending admin'],Constant::INVITATION_STATUS['Rejected']])
            ->orderBy('created_at','desc')
            ->paginate();
        if($request->invitation_type==Constant::INVITATION_TYPE['Contact Design']) {
            return view('pages.invitation.requests', compact('invitations'));
        }


    }
    public function guards(Invitation $invitation)
    {
        $guards=$invitation->guards()->paginate();
            return view('pages.invitation.guards', compact('guards'));



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        abort_if(Gate::denies('create_categories'), 403);

        return view('pages.invitation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvitationRequest $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
//        abort_if(Gate::denies('edit_categories'), 403);

        $invitation =Invitation::whereId($id)->first();
        return view('pages.invitation.edit',compact('invitation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InvitationRequest $request, $id)
    {
//        abort_if(Gate::denies('edit_categories'), 403);

        $invitation =Invitation::whereId($id)->first();
        $invitation->update([
            'address'=>$request->address,
            'latitude'=>$request->latitude,
            'longitude'=>$request->latitude,
            'date'=>$request->date
        ]);
        $mime = $request->file->getMimeType();

        if (strstr($mime, "video/")) {
            storeVideo([
                'value' => $request->file,
                'folderName' => Constant::INVITATION_IMAGE_FOLDER_NAME,
                'model' => $invitation,
                'saveInDatabase' => true,
                'file_key'=>Constant::FILE_KEY['Main'],
                'file_type'=>Constant::FILE_TYPE['Image'],

            ]);

        }
        elseif(strstr($mime, "image/gif")){
            storeGif([
                'value' => $request->file,
                'folderName' => Constant::INVITATION_IMAGE_FOLDER_NAME,
                'model' => $invitation,
                'saveInDatabase' => true,
                'file_key'=>Constant::FILE_KEY['Main'],
                'file_type'=>Constant::FILE_TYPE['Image'],

            ]);

        }
        else{

            storeImage([
                'value' => $request->file,
                'extension'=>$request->file->getClientOriginalExtension(),
                'folderName' => Constant::INVITATION_IMAGE_FOLDER_NAME,
                'model' => $invitation,
                'saveInDatabase' => true,
                'file_key'=>Constant::FILE_KEY['Main'],
                'file_type'=>Constant::FILE_TYPE['Image'],

            ]);

        }
        $invitation->update(['status'=>Constant::INVITATION_STATUS['Pending user approval']]);
        Notification::notify('users',
            Constant::NOTIFICATIONS_TYPE['Invitation Request'],
            [$invitation->user_id],
            $invitation->id,
            'invitation_confirmation_request');

        return redirect()->route('invitation.index',['invitation_type'=>Constant::INVITATION_TYPE['Contact Design']])->with('success','Updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        abort_if(Gate::denies('delete_categories'), 403);

        $invitation =Invitation::whereId($id)->first();
        if($invitation) {
            if (count($invitation->hubFiles) > 0) {
                foreach ($invitation->hubFiles as $hubFile) {
                    deleteImage($hubFile->get_folder_file(), $hubFile);
                }
            }
            $invitation->delete();
        }
        return redirect()->back()->with('success','Deleted');

    }

    public function changeStatus($id)
    {
//        abort_if(Gate::denies('delete_categories'), 403);

        $invitation = Invitation::whereId($id)->first();
        if ($invitation) {
            $invitation->update([
                'paid' => $invitation->paid == Constant::PAID_STATUS['Paid'] ?
                    Constant::PAID_STATUS['Not Paid'] : Constant::PAID_STATUS['Paid']
            ]);


            if ($invitation->paid == Constant::PAID_STATUS['Paid']) {
                foreach ($invitation->users as $user) {

                    Notification::notify('users',
                        Constant::NOTIFICATIONS_TYPE['Invitations'],
                        [$user->id],
                        $invitation->id,
                        'invitation_received');

                }
                Notification::notify('users',
                    Constant::NOTIFICATIONS_TYPE['Invitations'],
                    [$invitation->user_id],
                    $invitation->id,
                    'payment_approved');

            }
        }
        return redirect()->back()->with('success', 'Updated');

    }

    public function getPackagesByInvitationId()
    {
        $invitationId = request()->invitation_id;
        if(!isset($invitationId))
            return redirect()->back();


        $invitationPackages = InvitationPackage::orderBy('created_at','desc')
            ->with(['package','invitation'])
            ->where('invitation_id',$invitationId)

            ->paginate();

        return view('pages.invitation.packages', compact('invitationPackages'));

    }
    public function changePackageStatus(Request $request)
    {
//        abort_if(Gate::denies('delete_categories'), 403);
        $invitationPackageId =   $request->invitation_package_id;
        if(!isset($invitationPackageId))
            return redirect()->back();



        $invitationPackage =  InvitationPackage::find($invitationPackageId);

//        $invitation=Invitation::whereId($request->invitation_id)->first();
//        $invitationPackage =InvitationPackage::where(['invitation_id'=>$request->invitation_id,'package_id'=>$request->package_id])->first();
        
        if($invitationPackage) {
            $invitationPackage->update([
                'status'=>$invitationPackage->status==Constant::PAID_STATUS['Paid']?Constant::PAID_STATUS['Not Paid']:Constant::PAID_STATUS['Paid']
            ]);

            $invitationPackage->invitation->update([
                'paid' => $invitationPackage->invitation->paid == Constant::PAID_STATUS['Paid'] ?
                    Constant::PAID_STATUS['Not Paid'] : Constant::PAID_STATUS['Paid']
            ]);
  

            Notification::notify('users',
                Constant::NOTIFICATIONS_TYPE['Invitations'],
                [$invitationPackage->invitation->user_id],
                $invitationPackage->invitation_id,
                'payment_approved');

        }
        return redirect()->back()->with('success','Updated');
    }
}
