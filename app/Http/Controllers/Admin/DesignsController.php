<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DesignRequest;
use App\Models\Category;
use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

        $query = Design::with(['category', 'translations', 'hubFiles']);

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

        if ($request->hasFile('image')) {
            $this->storeDesignMedia($request->file('image'), $design);
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
        $design->load(['translations', 'hubFiles']);
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

        if ($request->hasFile('image')) {
            $this->removeDesignMediaFiles($design);
            $this->storeDesignMedia($request->file('image'), $design);
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
        $this->removeDesignMediaFiles($design);

        $categoryId = $design->category_id;
        $design->delete();

        return redirect()->route('designs.index', ['category_id' => $categoryId])->with('success', 'Deleted');
    }

    private function storeDesignMedia(UploadedFile $file, Design $design): void
    {
        $mime = $file->getMimeType();
        if ($mime && str_starts_with($mime, 'video/')) {
            storeVideo([
                'value' => $file,
                'folderName' => Constant::DESIGN_IMAGE_FOLDER_NAME,
                'model' => $design,
            ]);

            return;
        }

        storeImage([
            'value' => $file,
            'folderName' => Constant::DESIGN_IMAGE_FOLDER_NAME,
            'model' => $design,
            'saveInDatabase' => true,
        ]);
    }

    private function removeDesignMediaFiles(Design $design): void
    {
        $hub = $design->hubFiles;
        if (! $hub) {
            return;
        }

        $disk = Storage::disk(mediaDisk());
        $disk->delete($hub->bucket_name.'/'.$hub->path);
        if ((int) $hub->file_type === Constant::FILE_TYPE['Image']) {
            $disk->delete($hub->bucket_name.'/medium/'.$hub->path);
            $disk->delete($hub->bucket_name.'/thumbnail/'.$hub->path);
        }

        $hub->delete();
    }
}
