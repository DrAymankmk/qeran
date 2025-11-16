<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Admin\AppSettings\AppSettingsRequest;
use App\Http\Requests\V1\Admin\Link\LinksRequest;
use App\Models\AppSetting;
use App\Models\Link;
use Illuminate\Http\Request;

class AppSettingsController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function edit($key)
    {
        $appSetting=AppSetting::key($key)->first();
        return view('pages.app-settings.edit',compact('appSetting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $appSetting=AppSetting::key($request->key)->first();

        $appSetting->update([
            'value'=>$request->value
        ]);
        return redirect()->route('app-settings.edit',['key'=>$request->key])->with('success', 'تم التعديل بنجاح');
    }

}
