<?php

namespace Database\Seeders;

use App\Models\CmsSection;
use App\Models\CmsSectionTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
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
        CmsSectionTranslation::truncate();
        CmsSection::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // create hero section
        $heroSection = CmsSection::create([
            'page_id' => 2,
            'name' => 'hero',
            'type' => 'default',
            'template' => 'default',
            'order' => 1,
        ]);
        $heroSection->translateOrNew('en')->title = 'Hero Section';
        $heroSection->translateOrNew('ar')->title = '';
        $heroSection->translateOrNew('en')->subtitle = '';
        $heroSection->translateOrNew('ar')->subtitle = '';
        $heroSection->translateOrNew('en')->description = '';
        $heroSection->translateOrNew('ar')->description = '';
        $heroSection->save();

        // create home section
        $aboutSection = CmsSection::create([
            'page_id' => 2,
            'name' => 'about',
            'type' => 'default',
            'template' => 'default',
            'order' => 2,
        ]);
        $aboutSection->translateOrNew('en')->title = 'About';
        $aboutSection->translateOrNew('ar')->title = 'قِران: لمسة عصرية تغيّر طريقة دعوتك للضيوف';
        $aboutSection->translateOrNew('en')->subtitle = 'About us';
        $aboutSection->translateOrNew('ar')->subtitle = 'تطبيق قِران هو الحل العصري لمساعدة أصحاب المناسبات على إرسال دعواتهم بسهولة وأناقة.
يمنحكم التطبيق إمكانية إنشاء دعوات رقمية مميزة مزودة بـ QR Code خاص لكل مدعو، مع عرض جميع تفاصيل المناسبة بشكل منظم وجذاب يضفي لمسة فخامة على دعوتكم.
';
        $aboutSection->translateOrNew('en')->description = '';
        $aboutSection->translateOrNew('ar')->description = 'مع قِران ستتميّز دعوتكم بتجربة حديثة، راقية، ومختلفة عن الأسلوب التقليدي، لتمنح ضيوفكم انطباعًا لا يُنسى منذ اللحظة الأولى.';
        $aboutSection->save();

        // create services section
        $servicesSection = CmsSection::create([
            'page_id' => 2,
            'name' => 'services',
            'type' => 'default',
            'template' => 'default',
            'order' => 3,
        ]);
        $servicesSection->translateOrNew('en')->title = 'Services';
        $servicesSection->translateOrNew('ar')->title = 'ماذا يقدم لكم قِران';
        $servicesSection->translateOrNew('en')->subtitle = 'About us';
        $servicesSection->translateOrNew('ar')->subtitle = 'ما الذي يقدّمه لكم تطبيق قِران؟';
        $servicesSection->translateOrNew('en')->description = '';
        $servicesSection->translateOrNew('ar')->description = 'يمنحكم تطبيق قِران مجموعة من الأدوات الذكية التي تجعل إدارة الدعوات أكثر سهولة وتنظيمًا:';
        $servicesSection->save();

        // why choose us section
        $whyChooseUsSection = CmsSection::create([
            'page_id' => 2,
            'name' => 'why-choose-us',
            'type' => 'default',
            'template' => 'default',
            'order' => 4,
        ]);
        $whyChooseUsSection->translateOrNew('en')->title = 'Why Choose Us';
        $whyChooseUsSection->translateOrNew('ar')->title = 'لماذا تختار قِران؟';
        $whyChooseUsSection->translateOrNew('en')->subtitle = '';
        $whyChooseUsSection->translateOrNew('ar')->subtitle = '';
        $whyChooseUsSection->translateOrNew('en')->description = '';
        $whyChooseUsSection->translateOrNew('ar')->description = '';
        $whyChooseUsSection->save();

        // info section
        $infoSection = CmsSection::create([
            'page_id' => 2,
            'name' => 'info',
            'type' => 'default',
            'template' => 'default',
            'order' => 5,
        ]);
        $infoSection->translateOrNew('en')->title = '';
        $infoSection->translateOrNew('ar')->title = 'مع قِران، تصبح دعوتك تجربة راقية تجمع بين الفخامة والبساطة لتُبرز جمال مناسبتك. اختر قِران ليصل ضيوفك دعوة أنيقة تترك انطباعًا جميلًا لا يُنسى';
        $infoSection->translateOrNew('en')->subtitle = '';
        $infoSection->translateOrNew('ar')->subtitle = '';
        $infoSection->translateOrNew('en')->description = '';
        $infoSection->translateOrNew('ar')->description = '';
        $infoSection->save();

        // designs section
        $designsSection = CmsSection::create([
            'page_id' => 2,
            'name' => 'designs',
            'type' => 'default',
            'template' => 'default',
            'order' => 6,
        ]);
        $designsSection->translateOrNew('en')->title = '';
        $designsSection->translateOrNew('ar')->title = 'تشكيلة تصاميم راقية لمناسباتكم';
        $designsSection->translateOrNew('en')->subtitle = '';
        $designsSection->translateOrNew('ar')->subtitle = 'نقدّم لكم تصاميم دعوات عصرية ومختلفة  يمكنكم اختيار ما يناسبكم منها، ويقوم فريق قِران بتعديلها بالكامل وفق طلباتكم لتظهر بشكل يليق بمناسبتكم.(انسخ الكود وارسله لخدمة العملاء ) ';
        $designsSection->translateOrNew('en')->description = '';
        $designsSection->translateOrNew('ar')->description = '';
        $designsSection->save();

          // testimonials section
          $testimonialsSection = CmsSection::create([
            'page_id' => 2,
            'name' => 'testimonials',
            'type' => 'default',
            'template' => 'default',
            'order' => 7,
        ]);
        $testimonialsSection->translateOrNew('en')->title = '';
        $testimonialsSection->translateOrNew('ar')->title = 'آراء عملاءنا';
        $testimonialsSection->translateOrNew('en')->subtitle = '';
        $testimonialsSection->translateOrNew('ar')->subtitle = '';
        $testimonialsSection->translateOrNew('en')->description = '';
        $testimonialsSection->translateOrNew('ar')->description = '';
        $testimonialsSection->save();



        // guard section
        $guardAppSection = CmsSection::create([
            'page_id' => 2,
            'name' => 'guard-application',
            'type' => 'default',
            'template' => 'default',
            'order' => 8,
        ]);
        $guardAppSection->translateOrNew('en')->title = '';
        $guardAppSection->translateOrNew('ar')->title = 'تطبيق قِران الحارس';
        $guardAppSection->translateOrNew('en')->subtitle = '';
        $guardAppSection->translateOrNew('ar')->subtitle = 'تطبيق قِران الحارس" هو أداة ذكية لإدارة دعوات الزفاف، تتيح لك مسح الأكواد المرتبطة بكل دعوة بسهولة وسرعة، لضمان تنظيم ومتابعة حضور الضيوف بكل يسر."';
        $guardAppSection->translateOrNew('en')->description = '';
        $guardAppSection->translateOrNew('ar')->description = '';
        $guardAppSection->save();

        // contact
        $contactSection = CmsSection::create([
            'page_id' => 2,
            'name' => 'contact',
            'type' => 'default',
            'template' => 'default',
            'order' => 9,
        ]);
        $contactSection->translateOrNew('en')->title = '';
        $contactSection->translateOrNew('ar')->title = 'معلومات التواصل';
        $contactSection->translateOrNew('en')->subtitle = '';
        $contactSection->translateOrNew('ar')->subtitle = '';
        $contactSection->translateOrNew('en')->description = '';
        $contactSection->translateOrNew('ar')->description = '';
        $contactSection->save();



        // offers guard
        $offersSection = CmsSection::create([
            'page_id' => 2,
            'name' => 'offers',
            'type' => 'default',
            'template' => 'default',
            'order' => 10,
        ]);
        $offersSection->translateOrNew('en')->title = '';
        $offersSection->translateOrNew('ar')->title = 'عروضنا الخاصة';
        $offersSection->translateOrNew('en')->subtitle = '';
        $offersSection->translateOrNew('ar')->subtitle = 'تابعوا قسم العروض على موقعنا للحصول على أحدث الخصومات والكود الخاص لتفعيل العرض، واجعلوا تجربتكم مع قِران أكثر قيمة ومميزة.';
        $offersSection->translateOrNew('en')->description = '';
        $offersSection->translateOrNew('ar')->description = '';
        $offersSection->save();





//         services page section
        $servicesSection = CmsSection::create([
            'page_id' => 3,
            'name' => 'services',
            'type' => 'default',
            'template' => 'default',
            'order' => 1,
        ]);
        $servicesSection->translateOrNew('en')->title = '';
        $servicesSection->translateOrNew('ar')->title = 'خدماتنا';
        $servicesSection->translateOrNew('en')->subtitle = '';
        $servicesSection->translateOrNew('ar')->subtitle = 'أبرز مميزات تطبيق قِران:';
        $servicesSection->translateOrNew('en')->description = '';
        $servicesSection->translateOrNew('ar')->description = '';
        $servicesSection->save();

        //   packages section
        $packagesSection = CmsSection::create([
            'page_id' => 3,
            'name' => 'packages',
            'type' => 'default',
            'template' => 'default',
            'order' => 2,
        ]);
        $packagesSection->translateOrNew('en')->title = '';
        $packagesSection->translateOrNew('ar')->title = 'الباقات';
        $packagesSection->translateOrNew('en')->subtitle = '';
        $packagesSection->translateOrNew('ar')->subtitle = 'يقدّم تطبيق قِران باقاتٍ فاخرة مصممة بعناية، تمنحكم تجربة راقية وخيارات مرنة تُلائم جمال مناسباتكم';
        $packagesSection->translateOrNew('en')->description = '';
        $packagesSection->translateOrNew('ar')->description = '';
        $packagesSection->save();












        // about page sections
        $aboutSection = CmsSection::create([
            'page_id' => 4,
            'name' => 'about',
            'type' => 'default',
            'template' => 'default',
            'order' => 1,
        ]);
        $aboutSection->translateOrNew('en')->title = '';
        $aboutSection->translateOrNew('ar')->title = 'من نحن';
        $aboutSection->translateOrNew('en')->subtitle = '';
        $aboutSection->translateOrNew('ar')->subtitle = 'مرحباً بك في "قِرآن".......';
        $aboutSection->translateOrNew('en')->description = '';
        $aboutSection->translateOrNew('ar')->description = 'نؤمن في "قِرآن" بأن لكل مناسبة قصة، ولكل حدث رواية، وأن الحياة تُقاس باللحظات التي نتشاركها مع الأخرين.
من أجل ذلك، عمل فريقنا ولمدة عامين كاملين على تطبيق " قِرآن "، بوصفة تطبيق   يجعل من   عملية إرسال وتلقي بطاقات الدعوة تجربة سهلة سلسة يسيرة.
';
        $aboutSection->save();







        // faq page sections
        $faqSection = CmsSection::create([
            'page_id' => 5,
            'name' => 'faq',
            'type' => 'default',
            'template' => 'default',
            'order' => 1,
        ]);
        $faqSection->translateOrNew('en')->title = '';
        $faqSection->translateOrNew('ar')->title = 'الأسئلة الشائعة';
        $faqSection->translateOrNew('en')->subtitle = '';
        $faqSection->translateOrNew('ar')->subtitle = '';
        $faqSection->translateOrNew('en')->description = '';
        $faqSection->translateOrNew('ar')->description = '';
        $faqSection->save();


        // contact page sections
        $contactSection = CmsSection::create([
            'page_id' => 6,
            'name' => 'contact',
            'type' => 'default',
            'template' => 'default',
            'order' => 1,
        ]);
        $contactSection->translateOrNew('en')->title = '';
        $contactSection->translateOrNew('ar')->title = 'معلومات التواصل';
        $contactSection->translateOrNew('en')->subtitle = '';
        $contactSection->translateOrNew('ar')->subtitle = '';
        $contactSection->translateOrNew('en')->description = '';
        $contactSection->translateOrNew('ar')->description = '';
        $contactSection->save();
    }
}