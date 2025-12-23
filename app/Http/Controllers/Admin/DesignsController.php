<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DesignRequest;
use App\Models\Category;
use App\Models\Design;
use Illuminate\Http\Request;

class DesignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');

        $query = Design::with(['category', 'translations']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $designs = $query->orderBy('created_at', 'desc')->paginate();
        $category = $categoryId ? Category::find($categoryId) : null;

        return view('pages.designs.index', compact('designs', 'category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $categoryId = $request->get('category_id');
        $categories = Category::orderBy('created_at', 'desc')->get();
        $category = $categoryId ? Category::find($categoryId) : null;

        return view('pages.designs.create', compact('categories', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DesignRequest $request)
    {
        $validated = $request->validated();

        // Extract translation data
        $enData = [
            'name' => $validated['en']['name'] ?? null,
        ];
        $arData = [
            'name' => $validated['ar']['name'] ?? null,
        ];

        // Handle show_on - convert array to JSON
        if (isset($validated['show_on']) && is_array($validated['show_on'])) {
            // Filter out null/empty values
            $validated['show_on'] = array_filter($validated['show_on'], function($value) {
                return !empty($value);
            });
            // If empty array, set to null
            if (empty($validated['show_on'])) {
                $validated['show_on'] = null;
            }
        } else {
            $validated['show_on'] = null;
        }

        // Remove translation data from main data
        unset($validated['en'], $validated['ar'], $validated['image']);

        $design = Design::create($validated);

        // Save translations
        $design->translateOrNew('en')->name = $enData['name'];
        $design->translateOrNew('ar')->name = $arData['name'];
        $design->save();

        // Handle image upload
        if ($request->image) {
            storeImage([
                'value' => $request->image,
                'folderName' => Constant::DESIGN_IMAGE_FOLDER_NAME,
                'model' => $design,
                'saveInDatabase' => true
            ]);
        }

        return redirect()->route('designs.index', ['category_id' => $design->category_id])->with('success', 'Created');
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
    public function edit(Design $design)
    {
        $design->load('translations');
        $categories = Category::orderBy('created_at', 'desc')->get();

        return view('pages.designs.edit', compact('design', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DesignRequest $request, Design $design)
    {
        $validated = $request->validated();

        // Extract translation data
        $enData = [
            'name' => $validated['en']['name'] ?? null,
        ];
        $arData = [
            'name' => $validated['ar']['name'] ?? null,
        ];

        // Handle show_on - convert array to JSON
        if (isset($validated['show_on']) && is_array($validated['show_on'])) {
            // Filter out null/empty values
            $validated['show_on'] = array_filter($validated['show_on'], function($value) {
                return !empty($value);
            });
            // If empty array, set to null
            if (empty($validated['show_on'])) {
                $validated['show_on'] = null;
            }
        } else {
            $validated['show_on'] = null;
        }

        // Remove translation data from main data
        unset($validated['en'], $validated['ar'], $validated['image']);

        $design->update($validated);

        // Save translations
        $design->translateOrNew('en')->name = $enData['name'];
        $design->translateOrNew('ar')->name = $arData['name'];
        $design->save();

        // Handle image upload
        if ($request->image) {
            if ($design->hubFiles()->exists()) {
                deleteImage($design->image(), $design->hubFiles());
            }

            storeImage([
                'value' => $request->image,
                'folderName' => Constant::DESIGN_IMAGE_FOLDER_NAME,
                'model' => $design,
                'saveInDatabase' => true
            ]);
        }

        return redirect()->route('designs.index', ['category_id' => $design->category_id])->with('success', 'Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Design $design)
    {
        if ($design->hubFiles()->exists()) {
            deleteImage($design->image(), $design->hubFiles());
        }

        $categoryId = $design->category_id;
        $design->delete();

        return redirect()->route('designs.index', ['category_id' => $categoryId])->with('success', 'Deleted');
    }
}
