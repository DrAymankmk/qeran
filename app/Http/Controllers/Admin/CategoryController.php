<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Http\Controllers\Controller;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        abort_if(Gate::denies('all_categories'), 403);

        $categories=Category::orderBy('created_at','desc')->paginate();
        return view('pages.category.index',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        abort_if(Gate::denies('create_categories'), 403);

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
//        abort_if(Gate::denies('create_categories'), 403);
        $data= $request->validated();

        $category=Category::create([
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
        if($request->image){
            storeImage([
                'value' => $request->image,
                'folderName' => Constant::CATEGORY_IMAGE_FOLDER_NAME,
                'model' =>$category,
                'saveInDatabase' => true
            ]);
        }

        return redirect()->route('category.index')->with('success','Added');
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
//        abort_if(Gate::denies('edit_categories'), 403);

        $category=Category::whereId($id)->first();
        return view('pages.category.edit',compact('category'));
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
//        abort_if(Gate::denies('edit_categories'), 403);

        $data= $request->validated();

        // return $request;

        $category=Category::whereId($id)->first();
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
            ] );
        if($request->image){
            if ($category->hubFiles()->exists()) {
                deleteImage($category->image(), $category->hubFiles());
            }

            storeImage([
                'value' => $request->image,
                'folderName' => Constant::CATEGORY_IMAGE_FOLDER_NAME,
                'model' =>$category,
                'saveInDatabase' => true
            ]);
        }
        $category->save();
        return redirect()->route('category.index')->with('success','Updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        abort_if(Gate::denies('delete_categories'), 403);

        $category=Category::whereId($id)->first();
        if ($category->hubFiles()->exists()) {
            deleteImage($category->image(), $category->hubFiles());
        }
        $category->delete();

        return redirect()->route('category.index')->with('success','Deleted');

    }
}
