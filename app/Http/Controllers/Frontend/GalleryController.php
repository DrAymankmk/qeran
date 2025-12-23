<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Helpers\Constant;

class GalleryController extends Controller
{
    //
    public function index()
    {
        $categories = Category::where('active', Constant::CATEGORY_STATUS['Active'])
            ->whereNull('parent_id')
            ->with([
                'designs' => function ($query) {
                    $query->with(['translations', 'hubFiles']);
                },
                'translations',
            ])
            ->get();
        return view('frontend.pages.gallery.index', compact('categories'));
    }
}
