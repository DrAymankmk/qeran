<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    public function show($slug)
    {
        $page = CmsPage::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'activeSections.items' => function($query) {
                    $query->where('is_active', true)
                        ->with(['translations', 'media', 'links'])
                        ->orderBy('order');
                },
                'activeSections.translations',
                'activeSections.links',
                'links'
            ])
            ->firstOrFail();

        return view('frontend.cms.page', compact('page'));
    }
}

