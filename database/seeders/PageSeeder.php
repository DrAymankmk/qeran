<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\CmsPageTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear tables (delete in correct order to avoid foreign key issues)
        CmsPageTranslation::truncate();
        CmsPage::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create home page
        $home = CmsPage::create([
            'slug' => 'home',
            'name' => 'Home Page',
            'is_active' => true,
            'order' => 1,
        ]);

        // Translate home page
        $home->translateOrNew('en')->title = 'Home';
        $home->translateOrNew('en')->meta_description = 'Welcome to our home page';
        $home->translateOrNew('ar')->title = 'الرئيسية';
        $home->translateOrNew('ar')->meta_description = 'مرحباً بكم في الصفحة الرئيسية';
        $home->save();




        // Create services page
        $services = CmsPage::create([
            'slug' => 'services',
            'name' => 'Services Page',
            'is_active' => true,
            'order' => 1,
        ]);

        // Translate services page
        $services->translateOrNew('en')->title = 'Services';
        $services->translateOrNew('en')->meta_description = 'Welcome to our services page';
        $services->translateOrNew('ar')->title = 'الخدمات';
        $services->translateOrNew('ar')->meta_description = 'مرحباً بكم في صفحة الخدمات';
        $services->save();

        // Create about page
        $about = CmsPage::create([
            'slug' => 'about',
            'name' => 'About Page',
            'is_active' => true,
            'order' => 1,
        ]);

        // Translate services page
        $about->translateOrNew('en')->title = 'About';
        $about->translateOrNew('en')->meta_description = 'Welcome to about page';
        $about->translateOrNew('ar')->title = 'عن المنصه';
        $about->translateOrNew('ar')->meta_description = 'مرحباً بكم في صفحة عن المنصه';
        $about->save();


        // faq page
        $faq = CmsPage::create([
            'slug' => 'faq',
            'name' => 'FAQ Page',
            'is_active' => true,
            'order' => 1,
        ]);
        $faq->translateOrNew('en')->title = 'FAQ';
        $faq->translateOrNew('en')->meta_description = 'Welcome to FAQ page';
        $faq->translateOrNew('ar')->title = 'الأسئلة الشائعة';
        $faq->translateOrNew('ar')->meta_description = 'مرحباً بكم في صفحة الأسئلة الشائعة';
        $faq->save();



          // contact page
    $contact = CmsPage::create([
        'slug' => 'contact',
        'name' => 'Contact Page',
        'is_active' => true,
        'order' => 1,
    ]);
    $contact->translateOrNew('en')->title = 'Contact';
    $contact->translateOrNew('en')->meta_description = 'Welcome to contact page';
    $contact->translateOrNew('ar')->title = 'معلومات التواصل  ';
    $contact->translateOrNew('ar')->meta_description = 'مرحباً بكم في صفحة الاتصال';
    $contact->save();
    }


}