<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Services\External\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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


        $users = User::where('account_type',1)->whereNotNull('password')->orderBy('created_at', 'desc')->paginate();
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

    public function status(User $user)
    {

        $user->update([
            'verified' => 3
        ]);
        if($user->verified==3) {
            Notification::notify('users',
                Constant::NOTIFICATIONS_TYPE['Admin'],
                [$user->id],
                null,
                'Modern Invitation',
                __('You are blocked by admin!'));

        }
        return redirect()->back()->with('success', 'Updated');


    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UserRequest $request, $id)
    {
//        abort_if(Gate::denies('edit_users'), 403);

        $user = User::whereId($id)->first();
        $user->update($request->validated());
        if ($request->img) {
            $user->img = $request->img;
            $user->save();
        }
        if ($request->password) {
            $user->password = $request->password;
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'Update');

    }

}
