<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationsRequest;
use App\Models\Notification;
use App\Services\External\Notification as PushNotificationService;
use Illuminate\Support\Facades\Gate;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        abort_if(Gate::denies('access_notifications'), 403);
        $notifications=Notification::where('type',Constant::NOTIFICATIONS_TYPE['Admin'])->latest()->paginate();
        return view('pages.notifications.index',compact('notifications'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        abort_if(Gate::denies('create_notifications'), 403);
        return view('pages.notifications.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NotificationsRequest $request)
    {
//        abort_if(Gate::denies('create_notifications'), 403);
        // Notification::create($request->validated()+['type'=>Constant::NOTIFICATIONS_TYPE['Admin']]);
        PushNotificationService::notifyFor('users',$request->ar['title'],$request->ar['description']);
        return redirect()->route('notifications.index')->with('success','Added');


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
//        abort_if(Gate::denies('edit_notifications'), 403);
        $notification=Notification::whereId($id)->first();
        return view('pages.notifications.edit',compact('notification'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NotificationsRequest $request, Notification $notification)
    {
//        abort_if(Gate::denies('edit_notifications'), 403);
        $notification->update($request->validated());
        return redirect()->route('notifications.index')->with('success','Updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
//        abort_if(Gate::denies('delete_notifications'), 403);
        $notification->delete();
        return redirect()->route('notifications.index')->with('success','Deleted');

    }
}
