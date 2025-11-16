<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Models\Invitation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function loginForm(){
        return view('login.login');

    }
    public function login(Request $request){

        $credentials = array(
            'email' => $request->email,
            'password' =>$request->password
        );

        if (Auth::guard('admin')->attempt($credentials)) {

            return redirect()->route('admin.dashboard');

        }
        else{

            return redirect()->route('admin.login.form')->with('error','Email Or Password not correct');

        }

    }
    public function dashboard(){
        $usersCount=User::count();
        $invitationsCount=Invitation::count();
        $contactUsCount=ContactUs::count();
        $invitations=Invitation::orderBy('created_at','desc')
            ->with('user')
            ->whereHas('user')
            ->take(10)->get();
        $verifiedUsers11=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(11)->month)->count();
        $verifiedUsers10=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(10)->month)->count();
        $verifiedUsers9=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(9)->month)->count();
        $verifiedUsers8=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(8)->month)->count();
        $verifiedUsers7=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(7)->month)->count();
        $verifiedUsers6=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(6)->month)->count();
        $verifiedUsers5=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(5)->month)->count();
        $verifiedUsers4=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(4)->month)->count();
        $verifiedUsers3=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(3)->month)->count();
        $verifiedUsers2=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(2)->month)->count();
        $verifiedUsers1=User::where('verified',2)->whereMonth('created_at', Carbon::now()->subMonths(1)->month)->count();
        $verifiedUsers=User::where('verified',2)->whereMonth('created_at', Carbon::now()->month)->count();

        $notVerifiedUsers11=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(11)->month)->count();
        $notVerifiedUsers10=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(10)->month)->count();
        $notVerifiedUsers9=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(9)->month)->count();
        $notVerifiedUsers8=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(8)->month)->count();
        $notVerifiedUsers7=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(7)->month)->count();
        $notVerifiedUsers6=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(6)->month)->count();
        $notVerifiedUsers5=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(5)->month)->count();
        $notVerifiedUsers4=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(4)->month)->count();
        $notVerifiedUsers3=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(3)->month)->count();
        $notVerifiedUsers2=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(2)->month)->count();
        $notVerifiedUsers1=User::where('verified',0)->whereMonth('created_at', Carbon::now()->subMonths(1)->month)->count();
        $notVerifiedUsers=User::where('verified',0)->whereMonth('created_at', Carbon::now()->month)->count();

        $invitationsAppDesign11=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(11)->month)->count();
        $invitationsContactDesign11=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(11)->month)->count();
        $invitationsUserDesign11= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(11)->month)->count();

        $invitationsAppDesign10=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(10)->month)->count();
        $invitationsContactDesign10=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(10)->month)->count();
        $invitationsUserDesign10= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(10)->month)->count();

        $invitationsAppDesign9=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(9)->month)->count();
        $invitationsContactDesign9=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(9)->month)->count();
        $invitationsUserDesign9= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(9)->month)->count();

        $invitationsAppDesign8=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(8)->month)->count();
        $invitationsContactDesign8=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(8)->month)->count();
        $invitationsUserDesign8= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(8)->month)->count();

        $invitationsAppDesign7=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(7)->month)->count();
        $invitationsContactDesign7=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(7)->month)->count();
        $invitationsUserDesign7= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(7)->month)->count();

        $invitationsAppDesign6=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(6)->month)->count();
        $invitationsContactDesign6=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(6)->month)->count();
        $invitationsUserDesign6= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(6)->month)->count();

        $invitationsAppDesign5=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(5)->month)->count();
        $invitationsContactDesign5=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(5)->month)->count();
        $invitationsUserDesign5= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(5)->month)->count();

        $invitationsAppDesign4=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(4)->month)->count();
        $invitationsContactDesign4=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(4)->month)->count();
        $invitationsUserDesign4= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(4)->month)->count();

        $invitationsAppDesign3=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(3)->month)->count();
        $invitationsContactDesign3=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(3)->month)->count();
        $invitationsUserDesign3= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(3)->month)->count();

        $invitationsAppDesign2=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(2)->month)->count();
        $invitationsContactDesign2=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(2)->month)->count();
        $invitationsUserDesign2= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(2)->month)->count();

        $invitationsAppDesign1=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)->count();
        $invitationsContactDesign1=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)->count();
        $invitationsUserDesign1= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)->count();

        $invitationsAppDesign=Invitation::where('invitation_type',Constant::INVITATION_TYPE['App Design'])
        ->whereMonth('created_at', Carbon::now()->month)->count();
        $invitationsContactDesign=Invitation::where('invitation_type',Constant::INVITATION_TYPE['Contact Design'])
            ->whereMonth('created_at', Carbon::now()->month)->count();
        $invitationsUserDesign= Invitation::where('invitation_type',Constant::INVITATION_TYPE['User Design'])
            ->whereMonth('created_at', Carbon::now()->month)->count();


        $requestInvitations=Invitation::where('status',Constant::INVITATION_STATUS['Pending admin'])
            ->orderBy('created_at','desc')
            ->with('user')
            ->whereHas('user')
            ->take(10)
            ->get();
        $contactUs=ContactUs::orderBy('created_at','desc')
            ->take(10)
            ->get();
        return view('pages.index',compact('usersCount','invitationsCount'
        ,'contactUsCount','invitations','requestInvitations','contactUs',
        'verifiedUsers','verifiedUsers2','verifiedUsers3','verifiedUsers1','verifiedUsers4',
        'verifiedUsers5','verifiedUsers6','verifiedUsers7','verifiedUsers8','verifiedUsers9','verifiedUsers10',
        'verifiedUsers11',
        'notVerifiedUsers','notVerifiedUsers2','notVerifiedUsers3','notVerifiedUsers1','notVerifiedUsers4',
        'notVerifiedUsers5','notVerifiedUsers6','notVerifiedUsers7','notVerifiedUsers8','notVerifiedUsers9','notVerifiedUsers10',
        'notVerifiedUsers11',
        'invitationsAppDesign','invitationsAppDesign1','invitationsAppDesign2','invitationsAppDesign3','invitationsAppDesign4',
        'invitationsAppDesign5','invitationsAppDesign6','invitationsAppDesign7','invitationsAppDesign8',
        'invitationsAppDesign9','invitationsAppDesign10','invitationsAppDesign11',

        'invitationsContactDesign','invitationsContactDesign1','invitationsContactDesign2','invitationsContactDesign3','invitationsContactDesign4',
        'invitationsContactDesign5','invitationsContactDesign6','invitationsContactDesign7','invitationsContactDesign8',
        'invitationsContactDesign9','invitationsContactDesign10','invitationsContactDesign11',

        'invitationsUserDesign','invitationsUserDesign1','invitationsUserDesign2','invitationsUserDesign3','invitationsUserDesign4',
        'invitationsUserDesign5','invitationsUserDesign6','invitationsUserDesign7','invitationsUserDesign8',
        'invitationsUserDesign9','invitationsUserDesign10','invitationsUserDesign11',
        ));


    }
    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');

    }

}
