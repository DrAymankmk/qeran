<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Testimonial;
use App\Models\PromoCode;

class HomeController extends Controller
{
    //
    public function index()
    {
        $homePage = CmsPage::where('slug', 'home')->where('is_active', true)
        ->with([
            'activeSections.items' => function ($query) {
                $query->where('is_active', true)
                    ->with(['translations', 'media', 'links'])
                    ->orderBy('order');
            },
            'activeSections.translations',
            'activeSections.links',
            'links',
        ])
        ->firstOrFail();

        // categories with their designs
        $categories = Category::where('active', Constant::CATEGORY_STATUS['Active'])
            ->whereNull('parent_id')
            ->with([
                'designs' => function ($query) {
                    $query->with(['translations', 'hubFiles']);
                },
                'translations',
            ])
            ->get();

        // testimonials
        $testimonials = Testimonial::active()
            ->ordered()
            ->with(['translations', 'hubFiles'])
            ->get();

        // active promo codes (valid and not expired)
        $promoCodes = PromoCode::active()
            ->where('valid_date', '<=', now())
            ->where('expire_date', '>=', now())
            ->where(function($query) {
                $query->whereNull('usage_limit')
                      ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->with('package')
            ->orderBy('expire_date', 'asc')
            ->get();

        return view('frontend.pages.home.index', compact('homePage', 'categories', 'testimonials', 'promoCodes'));
    }
}
