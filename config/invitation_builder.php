<?php

/**
 * Invitation Builder — event types, themes, and feature catalog (Wooow-style).
 */
return [

    'defaults' => [
        'event_category' => 'wedding',
        'theme_template' => 16,
        'theme_mode' => 'dark',
        'opening_type' => 'envelope',
        'primary_color' => '#c9a962',
        'secondary_color' => '#e8b4b8',
        'background_color' => '#1a1520',
        'text_color' => '#faf6f0',
        'font_family' => 'Cairo',
        'animated_theme' => true,
        'music_enabled' => true,
        'welcome_enabled' => false,
    ],

    'event_types' => [
        'wedding' => ['label_ar' => 'زفاف', 'label_en' => 'Wedding', 'icon' => '💒'],
        'conference' => ['label_ar' => 'مؤتمر', 'label_en' => 'Conference', 'icon' => '🎤'],
        'seminar' => ['label_ar' => 'ندوة', 'label_en' => 'Seminar', 'icon' => '📚'],
        'medical_event' => ['label_ar' => 'فعالية طبية', 'label_en' => 'Medical Event', 'icon' => '🏥'],
        'business_meeting' => ['label_ar' => 'اجتماع عمل', 'label_en' => 'Business Meeting', 'icon' => '💼'],
        'product_launch' => ['label_ar' => 'إطلاق منتج', 'label_en' => 'Product Launch', 'icon' => '🚀'],
        'birthday' => ['label_ar' => 'عيد ميلاد', 'label_en' => 'Birthday', 'icon' => '🎂'],
        'graduation' => ['label_ar' => 'تخرج', 'label_en' => 'Graduation', 'icon' => '🎓'],
        'custom' => ['label_ar' => 'مناسبة مخصصة', 'label_en' => 'Custom Event', 'icon' => '✨'],
    ],

    'opening_types' => [
        'envelope' => ['label_ar' => 'ظرف متحرك', 'label_en' => 'Animated envelope'],
        'welcome' => ['label_ar' => 'شاشة ترحيب ثم الظرف', 'label_en' => 'Welcome screen + envelope'],
        'intro_video' => ['label_ar' => 'فيديو افتتاحي ثم الظرف', 'label_en' => 'Intro video + envelope'],
    ],

    'theme_modes' => [
        'dark' => ['label_ar' => 'داكن', 'label_en' => 'Dark'],
        'light' => ['label_ar' => 'فاتح', 'label_en' => 'Light'],
    ],

    /** Blade templates 1–21 (extend as you add templates). */
    'templates' => [
        1 => ['name' => 'Classic envelope', 'animated' => true, 'wooow_style' => false],
        16 => ['name' => 'Premium wax seal (Wooow)', 'animated' => true, 'wooow_style' => true],
        2 => ['name' => 'Scroll', 'animated' => true, 'wooow_style' => false],
        3 => ['name' => 'Modern card', 'animated' => true, 'wooow_style' => false],
        4 => ['name' => 'Gift box', 'animated' => true, 'wooow_style' => false],
        5 => ['name' => 'Minimal', 'animated' => false, 'wooow_style' => false],
    ],

    'features' => [
        'theme_selection' => ['label_ar' => 'اختيار القالب', 'label_en' => 'Theme selection'],
        'animated_themes' => ['label_ar' => 'قوالب متحركة', 'label_en' => 'Animated themes'],
        'video_background' => ['label_ar' => 'خلفية فيديو', 'label_en' => 'Video background'],
        'custom_branding' => ['label_ar' => 'هوية مخصصة', 'label_en' => 'Custom branding'],
        'dark_light' => ['label_ar' => 'وضع داكن / فاتح', 'label_en' => 'Dark / light mode'],
        'logo_upload' => ['label_ar' => 'رفع الشعار', 'label_en' => 'Logo upload'],
        'cover_image' => ['label_ar' => 'صورة الغلاف', 'label_en' => 'Cover image'],
        'background_media' => ['label_ar' => 'خلفية صورة/فيديو', 'label_en' => 'Background image/video'],
        'fonts' => ['label_ar' => 'الخطوط', 'label_en' => 'Fonts'],
        'colors' => ['label_ar' => 'الألوان', 'label_en' => 'Colors'],
        'custom_css' => ['label_ar' => 'CSS مخصص', 'label_en' => 'Custom CSS'],
        'opening_envelope' => ['label_ar' => 'ظرف افتتاحي', 'label_en' => 'Animated envelope'],
        'opening_animation' => ['label_ar' => 'حركة افتتاح', 'label_en' => 'Opening animation'],
        'welcome_screen' => ['label_ar' => 'شاشة ترحيب', 'label_en' => 'Welcome screen'],
        'background_music' => ['label_ar' => 'موسيقى خلفية', 'label_en' => 'Background music'],
        'intro_video' => ['label_ar' => 'فيديو افتتاحي', 'label_en' => 'Intro video'],
    ],

];
