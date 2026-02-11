<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Admin\AppSettings\AppSettingsRequest;
use App\Http\Requests\V1\Admin\Link\LinksRequest;
use App\Models\AppSetting;
use App\Models\Link;
use Mpdf\Mpdf;
use Illuminate\Http\Request;

class AppSettingsController extends Controller
{
    public function index()
    {
        $appSettings = AppSetting::get();
        return view('pages.app-settings.index', compact('appSettings'));
    }

    public function create()
    {
        return view('pages.app-settings.create');
    }

    public function edit($key)
    {
        $appSetting = AppSetting::key($key)->first();
        return view('pages.app-settings.edit', compact('appSetting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:app_settings,key',
            'value' => 'required|string',
            'type' => 'required|string|in:text,video,number,email,textarea,editor'
        ]);

        $appSetting = AppSetting::create([
            'key' => $request->key,
            'value' => $request->value,
            'type' => $request->type
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('admin.created-successfully'),
                'data' => $appSetting
            ]);
        }

        return redirect()->route('app-settings.index')->with('success', __('admin.created-successfully'));
    }

    public function update(Request $request, $key)
    {
        $request->validate([
            'value' => 'required|string'
        ]);

        $appSetting = AppSetting::key($key)->first();

        if (!$appSetting) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.setting-not-found')
                ], 404);
            }
            return redirect()->back()->with('error', __('admin.setting-not-found'));
        }

        $appSetting->update([
            'value' => $request->value
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('admin.updated-successfully'),
                'data' => $appSetting
            ]);
        }

        return redirect()->route('app-settings.edit', ['key' => $key])->with('success', __('admin.updated-successfully'));
    }

    // destroy
    public function destroy($id)
    {
        $appSetting = AppSetting::find($id);
        $appSetting->delete();
        return redirect()->route('app-settings.index')->with('success', __('admin.deleted-successfully'));
    }

    public function exportPdf()
    {
        $appSettings = AppSetting::orderBy('created_at', 'desc')->get();

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
        $html = view('pages.app-settings.pdf-export', compact('appSettings'))->render();

        $mpdf->WriteHTML($html);

        $filename = 'app-settings_' . date('Y-m-d_His') . '.pdf';

        return $mpdf->Output($filename, 'D'); // D = Download
    }
}