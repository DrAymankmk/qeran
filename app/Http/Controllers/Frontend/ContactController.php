<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsPage;
class ContactController extends Controller
{
    //
    public function index()
    {
        $contactPage = CmsPage::where('slug', 'contact')->where('is_active', true)
        ->with([
            'activeSections.items' => function($query) {
                $query->where('is_active', true)
                    ->with(['translations', 'media', 'links'])
                    ->orderBy('order');
            },
        ])
        ->firstOrFail();
        return view('frontend.pages.contact.index', compact('contactPage'));
    }
}
