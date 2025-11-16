<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use App\Models\Invitation;
use App\Models\Package;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        abort_if(Gate::denies('all_categories'), 403);
        $packages=Package::orderBy('created_at','desc')
            ->when($request->invitation_id,function ($query)use($request){
                $query->whereHas('invitations',function ($query)use($request){
                    $query->where('invitation_id',$request->invitation_id);
                });
            })
            ->paginate();
        if($request->invitation_id){
            return view('pages.invitation.packages', compact('packages'));
        }
        return view('pages.packages.index', compact('packages'));

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        abort_if(Gate::denies('create_categories'), 403);

        return view('pages.packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PackageRequest $request)
    {
        Package::create($request->validated());
        return redirect()->route('package.index')->with('success','Created');

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
    public function edit(Package $package)
    {
//        abort_if(Gate::denies('edit_categories'), 403);

        return view('pages.packages.edit',compact('package'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PackageRequest $request, Package $package)
    {
//        abort_if(Gate::denies('edit_categories'), 403);
        $package->update($request->validated());


        return redirect()->route('package.index')->with('success','Updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Package $package)
    {
//        abort_if(Gate::denies('delete_categories'), 403);

        $package->delete();

        return redirect()->back()->with('success','Deleted');

    }
}
