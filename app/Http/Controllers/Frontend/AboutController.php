<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsPage;
class AboutController extends Controller
{
    public function index()
    {
        $aboutPage = CmsPage::where('slug', 'about')->where('is_active', true)
        ->with([
            'activeSections.items' => function($query) {
                $query->where('is_active', true)
                    ->with(['translations', 'media', 'links'])
                    ->orderBy('order');
            },
        ])
        ->firstOrFail();
        return view('frontend.pages.about.index', compact('aboutPage'));
    }
}