<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromoCodeRequest;
use App\Models\Package;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promoCodes = PromoCode::with('package')->orderBy('created_at', 'desc')->get();
        $packages = Package::active()->get();

        return view('pages.promo-code.index', compact('promoCodes', 'packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PromoCodeRequest $request)
    {
        $data = $request->validated();

        // Convert code to uppercase
        $data['code'] = strtoupper($data['code']);

        // Handle is_active checkbox (if not sent, default to false)
        $data['is_active'] = $request->has('is_active') && $request->is_active == '1';

        // If package_id is empty string or not provided, set to null (for all packages)
        if (!isset($data['package_id']) || $data['package_id'] === '') {
            $data['package_id'] = null;
        }

        $promoCode = PromoCode::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('admin.created-successfully'),
                'data' => $promoCode->load('package')
            ]);
        }

        return redirect()->route('promo-code.index')->with('success', __('admin.created-successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $promoCode = PromoCode::with('package')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $promoCode
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    public function update(PromoCodeRequest $request, $id)
    {
        $promoCode = PromoCode::findOrFail($id);
        $data = $request->validated();

        // Convert code to uppercase
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        // Handle is_active checkbox (if not sent, default to false)
        if (isset($data['is_active'])) {
            $data['is_active'] = $request->has('is_active') && $request->is_active == '1';
        }

        // If package_id is empty string or not provided, set to null (for all packages)
        if (!isset($data['package_id']) || $data['package_id'] === '') {
            $data['package_id'] = null;
        }

        $promoCode->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('admin.updated-successfully'),
                'data' => $promoCode->load('package')
            ]);
        }

        return redirect()->route('promo-code.index')->with('success', __('admin.updated-successfully'));
    }


    public function destroy($id)
    {
        $promoCode = PromoCode::findOrFail($id);
        $promoCode->delete();

        return redirect()->route('promo-code.index')->with('success', __('admin.deleted-successfully'));
    }

    public function exportPdf()
    {
        $promoCodes = PromoCode::orderBy('created_at', 'desc')->get();

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
        $html = view('pages.promo-code.pdf-export', compact('promoCodes'))->render();

        $mpdf->WriteHTML($html);

        $filename = 'promo-codes_' . date('Y-m-d_His') . '.pdf';

        return $mpdf->Output($filename, 'D'); // D = Download
    }
}
