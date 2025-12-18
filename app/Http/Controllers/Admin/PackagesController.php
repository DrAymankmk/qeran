<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use App\Models\Invitation;
use App\Models\Package;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

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
        $validated = $request->validated();
        
        // Extract translation data
        $enData = [
            'title' => $validated['en']['title'] ?? null,
            'subtitle' => $validated['en']['subtitle'] ?? null,
            'content' => $validated['en']['content'] ?? null,
        ];
        $arData = [
            'title' => $validated['ar']['title'] ?? null,
            'subtitle' => $validated['ar']['subtitle'] ?? null,
            'content' => $validated['ar']['content'] ?? null,
        ];
        
        // Remove translation data from main data
        unset($validated['en'], $validated['ar'], $validated['title'], $validated['subtitle'], $validated['content']);
        
        $package = Package::create($validated);
        
        // Save translations
        $package->translateOrNew('en')->title = $enData['title'];
        $package->translateOrNew('en')->subtitle = $enData['subtitle'];
        $package->translateOrNew('en')->content = $enData['content'];
        $package->translateOrNew('ar')->title = $arData['title'];
        $package->translateOrNew('ar')->subtitle = $arData['subtitle'];
        $package->translateOrNew('ar')->content = $arData['content'];
        $package->save();
        
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
        
        $package->load('translations');

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
        
        $validated = $request->validated();
        
        // Extract translation data
        $enData = [
            'title' => $validated['en']['title'] ?? null,
            'subtitle' => $validated['en']['subtitle'] ?? null,
            'content' => $validated['en']['content'] ?? null,
        ];
        $arData = [
            'title' => $validated['ar']['title'] ?? null,
            'subtitle' => $validated['ar']['subtitle'] ?? null,
            'content' => $validated['ar']['content'] ?? null,
        ];
        
        // Remove translation data from main data
        unset($validated['en'], $validated['ar'], $validated['title'], $validated['subtitle'], $validated['content']);
        
        $package->update($validated);
        
        // Save translations
        $package->translateOrNew('en')->title = $enData['title'];
        $package->translateOrNew('en')->subtitle = $enData['subtitle'];
        $package->translateOrNew('en')->content = $enData['content'];
        $package->translateOrNew('ar')->title = $arData['title'];
        $package->translateOrNew('ar')->subtitle = $arData['subtitle'];
        $package->translateOrNew('ar')->content = $arData['content'];
        $package->save();

        return redirect()->route('package.index')->with('success','Updated');
    }

   
    public function destroy(Package $package)
    {

        $package->delete();

        return redirect()->back()->with('success','Deleted');

    }

     public function packageExportPdf()
    {
        $packages = Package::orderBy('created_at', 'desc')->get();

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
        $html = view('pages.packages.pdf-export', compact('packages'))->render();

        $mpdf->WriteHTML($html);

        $filename = 'packages_' . date('Y-m-d_His') . '.pdf';

        return $mpdf->Output($filename, 'D'); // D = Download
    }
}
