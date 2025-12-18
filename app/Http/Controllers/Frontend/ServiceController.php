<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsPage;
use App\Models\Package;

class ServiceController extends Controller
{
    //

    public function index()
    {
        $servicesPage = CmsPage::where('slug', 'services')->where('is_active', true)
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

        $homePage = CmsPage::where('slug', 'home')->where('is_active', true)
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

        $packages = Package::where('package_invitation_type', Constant::INVITATION_TYPE['User Design'])
        ->take('3')
        ->get();
        return view('frontend.pages.services.index', compact('servicesPage', 'homePage', 'packages'));
    }
}