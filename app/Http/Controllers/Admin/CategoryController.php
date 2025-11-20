<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Mpdf\Mpdf;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $categories = Category::orderBy('created_at', 'desc')->get();
        return view('pages.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('pages.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        $category = Category::create([
            'ar'        => [
                'title'         => $request->ar['title'],
                'name'          => $request->ar['name'],
                'slug'          => slug($request->ar['name']),
                'description'   => $request->ar['description'],
            ],
            'en'        => [
                'title'         => $request->en['title'],
                'name'          => $request->en['name'],
                'slug'          => slug($request->en['name']),
                'description'   => $request->en['description'],
            ]
        ]);
        if ($request->image) {
            storeImage([
                'value' => $request->image,
                'folderName' => Constant::CATEGORY_IMAGE_FOLDER_NAME,
                'model' => $category,
                'saveInDatabase' => true
            ]);
        }

        return redirect()->route('category.index')->with('success', 'Added');
    }


    public function show($id)
    {
        //
        $category = Category::findOrFail($id);

        return  response()->json([
            'id' => $category->id,
            'en_name'=>$category->getTranslation('en')->name,
            'ar_name'=>$category->getTranslation('ar')->name,
            'en_title'=>$category->getTranslation('en')->title,
            'ar_title'=>$category->getTranslation('ar')->title,
            'en_description'=>$category->getTranslation('en')->description,
            'ar_description'=>$category->getTranslation('ar')->description,
            'image' => $category->image(),
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
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

        $category = Category::whereId($id)->first();
        return view('pages.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {

        $data = $request->validated();


        $category = Category::whereId($id)->first();
        $category->update([
            'ar'        => [
                'title'         => $request->ar['title'],
                'name'          => $request->ar['name'],
                'slug'          => slug($request->ar['name']),
                'description'   => $request->ar['description'],
            ],
            'en'        => [
                'title'         => $request->en['title'],
                'name'          => $request->en['name'],
                'slug'          => slug($request->en['name']),
                'description'   => $request->en['description'],
            ]
        ]);
        if ($request->image) {
            if ($category->hubFiles()->exists()) {
                deleteImage($category->image(), $category->hubFiles());
            }

            storeImage([
                'value' => $request->image,
                'folderName' => Constant::CATEGORY_IMAGE_FOLDER_NAME,
                'model' => $category,
                'saveInDatabase' => true
            ]);
        }
        $category->save();
        return redirect()->route('category.index')->with('success', 'Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $category = Category::whereId($id)->first();
        if ($category->hubFiles()->exists()) {
            deleteImage($category->image(), $category->hubFiles());
        }
        $category->delete();

        return redirect()->route('category.index')->with('success', 'Deleted');
    }

    /**
     * Export categories to PDF with Arabic support
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        
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
        $html = view('pages.category.pdf-export', compact('categories'))->render();
        
        $mpdf->WriteHTML($html);
        
        $filename = 'categories_' . date('Y-m-d_His') . '.pdf';
        
        return $mpdf->Output($filename, 'D'); // D = Download
    }
}
