<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsPage;
class FaqController extends Controller
{
    //
    public function index()
    {
        $faqPage = CmsPage::where('slug', 'faq')->where('is_active', true)
        ->with([
            'activeSections.items' => function($query) {
                $query->where('is_active', true)
                    ->with(['translations', 'media', 'links'])
                    ->orderBy('order');
            },
        ])
        ->firstOrFail();
        return view('frontend.pages.faq.index', compact('faqPage'));
    }
}