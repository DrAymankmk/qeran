<?php

/**
 * Invitation Builder — animated themes, envelope, details, blocks (Wooow-style).
 * Add new animated themes to `animated_themes` (not legacy blade templates 1–21).
 */
return [

    'defaults' => [
        'event_category' => 'wedding',
        'theme_slug' => 'opening-gold-bloom',
        'renderer' => 'builder-wedding',
        'theme_template' => 0,
        'theme_mode' => 'dark',
        'opening_type' => 'envelope',
        'primary_color' => '#c9a962',
        'secondary_color' => '#e8b4b8',
        'background_color' => '#1a1520',
        'text_color' => '#faf6f0',
        'font_family' => 'Cairo',
        'headline_font' => 'Playfair Display',
        'animated_theme' => true,
        'music_enabled' => false,
        'welcome_enabled' => false,
        'envelope_color' => 'cream',
        'envelope_shape' => 'classic',
        'seal_style' => 'wax_classic',
        'seal_color' => '#a31830',
        'envelope_initials' => '',
        'envelope_image_url' => '',
        'envelope_image_ref' => '',
        'opening_headline' => '',
        'date_position' => 'center',
        'block_accent_color' => '#c9a962',
        'block_floral_border' => true,
        'blocks' => ['countdown', 'event_details', 'venue'],
        'venue_name' => '',
        'venue_location' => '',
        'ceremony_note' => '',
        'reception_time' => '',
        'reception_note' => '',
        'details_section_title' => '',
        'details_section_label' => 'جميع التفاصيل',
    ],

    /** When false, built-in themes below are hidden from the admin picker (still work for saved invitations). */
    'show_builtin_animated_themes' => false,

    'theme_categories' => [
        'opening' => ['label_ar' => 'افتتاحية', 'label_en' => 'Opening'],
        'romantic' => ['label_ar' => 'رومانسي', 'label_en' => 'Romantic'],
        'tropical' => ['label_ar' => 'استوائي', 'label_en' => 'Tropical'],
        'elegant' => ['label_ar' => 'أنيق', 'label_en' => 'Elegant'],
        'modern' => ['label_ar' => 'عصري', 'label_en' => 'Modern'],
        'fairytale' => ['label_ar' => 'خرافي', 'label_en' => 'Fairytale'],
        'minimal' => ['label_ar' => 'بسيط', 'label_en' => 'Minimal'],
    ],

    /**
     * Opening animation themes (hero background video + palette).
     * opening_video_url: MP4 played in builder-wedding hero section.
     */
    'animated_themes' => [
        'opening-gold-bloom' => [
            'name_ar' => 'نموذج 1',
            'name_en' => 'Gold Bloom Opening',
            'category' => 'opening',
            'media_type' => 'video',
            'opening_video_url' => 'https://www.wooowinvites.com/assets/palm-zoom-theme-DTmwX1Yh.mp4',
            'preview' => '#1a1520',
            'primary_color' => '#c8a97a',
            'secondary_color' => '#e8b4b8',
            'background_color' => '#1a1520',
            'text_color' => '#faf7f2',
            'renderer' => 'builder-wedding',
        ],
        'opening-soft-glow' => [
            'name_ar' => 'نموذج 2',
            'name_en' => 'Soft Glow Opening',
            'category' => 'opening',
            'media_type' => 'video',
            'opening_video_url' => 'https://www.wooowinvites.com/assets/castle-theme-DW5muDbc.mp4',
            'preview' => '#2d1f28',
            'primary_color' => '#c9a962',
            'secondary_color' => '#e8b4b8',
            'background_color' => '#2d1f28',
            'text_color' => '#faf6f0',
            'renderer' => 'builder-wedding',
        ],
    ],

    /** Legacy theme slugs → current opening theme (saved invitations). */
    'theme_slug_aliases' => [
        'elegant-wedding' => 'opening-gold-bloom',
        'romantic-blush' => 'opening-soft-glow',
    ],

    'envelope_colors' => [
        'cream' => ['label_ar' => 'كريمي', 'hex' => '#f5f0e6', 'swatch' => '#f5f0e6'],
        'blush' => ['label_ar' => 'وردي فاتح', 'hex' => '#f4d4d4', 'swatch' => '#f4d4d4'],
        'navy' => ['label_ar' => 'كحلي', 'hex' => '#1e3a5f', 'swatch' => '#1e3a5f'],
        'sage' => ['label_ar' => 'أخضر زيتوني', 'hex' => '#9caf88', 'swatch' => '#9caf88'],
        'gold' => ['label_ar' => 'ذهبي', 'hex' => '#d4af37', 'swatch' => '#d4af37'],
        'blush_pink' => ['label_ar' => 'وردي بلاش', 'hex' => '#e8b4b8', 'swatch' => '#e8b4b8'],
        'ivory' => ['label_ar' => 'عاجي', 'hex' => '#fffff0', 'swatch' => '#fffff0'],
        'charcoal' => ['label_ar' => 'فحمي', 'hex' => '#36454f', 'swatch' => '#36454f'],
        'burgundy' => ['label_ar' => 'عنابي', 'hex' => '#722f37', 'swatch' => '#722f37'],
    ],

    /**
     * Built 3D envelope geometry (used when no custom image, and for flap animation).
     */
    'envelope_shapes' => [
        // 'classic' => ['label_ar' => 'كلاسيكي مثلث', 'label_en' => 'Classic triangle flap'],
        'european' => ['label_ar' => 'أوروبي عميق', 'label_en' => 'Deep European flap'],
        // 'square' => ['label_ar' => 'حديث مربع', 'label_en' => 'Modern square flap'],
        // 'luxe' => ['label_ar' => 'فاخر مزدوج', 'label_en' => 'Luxe double-edge'],
        // 'vintage' => ['label_ar' => 'عتيق بنسيج', 'label_en' => 'Vintage textured'],
        // 'pocket' => ['label_ar' => 'جيب جانبي', 'label_en' => 'Side pocket'],
    ],

    /**
     * Stock envelope images (under public/). More files in public/images/invitation-builder/envelopes/ are picked up automatically.
     */
    'envelope_images' => [
        'classic-cream' => ['label_ar' => 'كريمي كلاسيكي', 'path' => 'images/invitation-builder/envelopes/classic-cream.svg'],
        'blush-rose' => ['label_ar' => 'وردي رومانسي', 'path' => 'images/invitation-builder/envelopes/blush-rose.svg'],
        'navy-gold' => ['label_ar' => 'كحلي ذهبي', 'path' => 'images/invitation-builder/envelopes/navy-gold.svg'],
        'ivory-liner' => ['label_ar' => 'عاجي ببطانة', 'path' => 'images/invitation-builder/envelopes/ivory-liner.svg'],
        'sage-garden' => ['label_ar' => 'أخضر زيتوني', 'path' => 'images/invitation-builder/envelopes/sage-garden.svg'],
        'burgundy-wax' => ['label_ar' => 'عنابي شمع', 'path' => 'images/invitation-builder/envelopes/burgundy-wax.svg'],
        'charcoal-minimal' => ['label_ar' => 'فحمي عصري', 'path' => 'images/invitation-builder/envelopes/charcoal-minimal.svg'],
        'gold-foil' => ['label_ar' => 'حافة ذهبية', 'path' => 'images/invitation-builder/envelopes/gold-foil.svg'],
        'parchment-vintage' => ['label_ar' => 'رق قديم', 'path' => 'images/invitation-builder/envelopes/parchment-vintage.svg'],
        'peacock-elegant' => ['label_ar' => 'أنيق تركواز', 'path' => 'images/invitation-builder/envelopes/peacock-elegant.svg'],
        'lilac-dream' => ['label_ar' => 'ليلكي حالم', 'path' => 'images/invitation-builder/envelopes/lilac-dream.svg'],
        'terracotta-rustic' => ['label_ar' => 'تراكوتا ريفي', 'path' => 'images/invitation-builder/envelopes/terracotta-rustic.svg'],
    ],

    'seal_palette_colors' => [
        'crimson' => '#a31830',
        'burgundy' => '#7a1830',
        'rose' => '#c44a6a',
        'navy' => '#1e3a5f',
        'emerald' => '#1a6b4a',
        'gold-wax' => '#b8860b',
        'bronze' => '#8b5a2b',
        'gold' => '#c9a227',
        'silver' => '#8a8a96',
        'blush' => '#d4899a',
        'ivory' => '#e8e0d0',
    ],

    'seal_styles' => [
        'wax_classic' => ['label_ar' => 'شمع دائري', 'label_en' => 'Round Wax', 'icon' => '🔴', 'shape' => 'wax-round', 'palette' => 'crimson', 'default_color' => '#a31830', 'ring' => true, 'drip' => true],
        'wax_oval' => ['label_ar' => 'شمع بيضوي', 'label_en' => 'Oval Wax', 'icon' => '⭕', 'shape' => 'wax-oval', 'palette' => 'crimson', 'default_color' => '#8b1428', 'ring' => true, 'drip' => true],
        'wax_hexagon' => ['label_ar' => 'شمع سداسي', 'label_en' => 'Hexagon Wax', 'icon' => '⬡', 'shape' => 'wax-hex', 'palette' => 'burgundy', 'default_color' => '#7a1830', 'ring' => false, 'drip' => true],
        'wax_octagon' => ['label_ar' => 'شمع ثماني', 'label_en' => 'Octagon Wax', 'icon' => '🛑', 'shape' => 'wax-oct', 'palette' => 'burgundy', 'default_color' => '#6b1530', 'ring' => true, 'drip' => false],
        'wax_heart' => ['label_ar' => 'شمع قلب', 'label_en' => 'Heart Wax', 'icon' => '♥', 'shape' => 'wax-heart', 'palette' => 'rose', 'default_color' => '#c44a6a', 'ring' => false, 'drip' => true],
        'wax_shield' => ['label_ar' => 'شمع درع', 'label_en' => 'Shield Wax', 'icon' => '🛡', 'shape' => 'wax-shield', 'palette' => 'crimson', 'default_color' => '#9b1c32', 'ring' => true, 'drip' => false],
        'wax_square' => ['label_ar' => 'شمع مربع', 'label_en' => 'Square Wax', 'icon' => '▣', 'shape' => 'wax-square', 'palette' => 'navy', 'default_color' => '#1e3a5f', 'ring' => true, 'drip' => true],
        'wax_star' => ['label_ar' => 'شمع نجمة', 'label_en' => 'Star Wax', 'icon' => '★', 'shape' => 'wax-star', 'palette' => 'gold-wax', 'default_color' => '#b8860b', 'ring' => false, 'drip' => true],
        'wax_emerald' => ['label_ar' => 'شمع أخضر', 'label_en' => 'Emerald Wax', 'icon' => '💚', 'shape' => 'wax-round', 'palette' => 'emerald', 'default_color' => '#1a6b4a', 'ring' => true, 'drip' => true],
        'wax_navy' => ['label_ar' => 'شمع كحلي', 'label_en' => 'Navy Wax', 'icon' => '🔵', 'shape' => 'wax-oval', 'palette' => 'navy', 'default_color' => '#152d4a', 'ring' => true, 'drip' => false],
        'wax_double_ring' => ['label_ar' => 'حلقتان', 'label_en' => 'Double Ring', 'icon' => '◎', 'shape' => 'wax-double', 'palette' => 'crimson', 'default_color' => '#b81c38', 'ring' => true, 'drip' => false],
        'floral_emboss' => ['label_ar' => 'نقش زهري', 'label_en' => 'Floral Emboss', 'icon' => '🌸', 'shape' => 'floral-round', 'palette' => 'blush', 'default_color' => '#d4899a', 'ring' => false, 'drip' => false],
        'gold_foil' => ['label_ar' => 'ذهبي لامع', 'label_en' => 'Gold Foil', 'icon' => '✨', 'shape' => 'foil-round', 'palette' => 'gold', 'default_color' => '#c9a227', 'ring' => true, 'drip' => false],
        'silver_foil' => ['label_ar' => 'فضي لامع', 'label_en' => 'Silver Foil', 'icon' => '⚪', 'shape' => 'foil-round', 'palette' => 'silver', 'default_color' => '#9ca3af', 'ring' => true, 'drip' => false],
        'royal_crest' => ['label_ar' => 'شعار ملكي', 'label_en' => 'Royal Crest', 'icon' => '👑', 'shape' => 'royal-shield', 'palette' => 'gold', 'default_color' => '#d4af37', 'ring' => true, 'drip' => false],
        'bronze_seal' => ['label_ar' => 'برونزي', 'label_en' => 'Bronze Seal', 'icon' => '🟤', 'shape' => 'wax-hex', 'palette' => 'bronze', 'default_color' => '#8b5a2b', 'ring' => false, 'drip' => true],
        'ivory_emboss' => ['label_ar' => 'نقش عاجي', 'label_en' => 'Ivory Emboss', 'icon' => '🤍', 'shape' => 'emboss-round', 'palette' => 'ivory', 'default_color' => '#e8e0d0', 'ring' => true, 'drip' => false],
    ],

    'fonts' => [
        'Cairo' => 'Cairo',
        'Playfair Display' => 'Playfair Display',
        'Amiri' => 'Amiri',
        'Tajawal' => 'Tajawal',
        'Cormorant Garamond' => 'Cormorant Garamond',
        'Great Vibes' => 'Great Vibes',
        'Lora' => 'Lora',
        'Merriweather' => 'Merriweather',
        'Montserrat' => 'Montserrat',
        'Raleway' => 'Raleway',
        'Libre Baskerville' => 'Libre Baskerville',
        'Dancing Script' => 'Dancing Script',
        'Josefin Sans' => 'Josefin Sans',
        'Cormorant' => 'Cormorant',
        'EB Garamond' => 'EB Garamond',
        'Noto Naskh Arabic' => 'Noto Naskh Arabic',
    ],

    'date_positions' => [
        'top' => ['label_ar' => 'أعلى البطاقة', 'label_en' => 'Top'],
        'center' => ['label_ar' => 'وسط البطاقة', 'label_en' => 'Center'],
        'bottom' => ['label_ar' => 'أسفل البطاقة', 'label_en' => 'Bottom'],
    ],

    'font_weights' => [
        '300' => 'خفيف (300)',
        '400' => 'عادي (400)',
        '500' => 'متوسط (500)',
        '600' => 'شبه عريض (600)',
        '700' => 'عريض (700)',
    ],

    /**
     * Per-block appearance (stored in settings.block_data[block_key]).
     * Types: optional_color, font, font_size (px), font_weight.
     * Optional group + group_label_ar render a subheading in the block editor.
     */
    'block_style_fields' => [
        'background_color' => ['type' => 'optional_color', 'label_ar' => 'لون خلفية القسم', 'default' => '', 'group' => 'base', 'group_label_ar' => 'الخلفية والخطوط'],
        'font_family' => ['type' => 'font', 'label_ar' => 'خط النص', 'default' => '', 'group' => 'base'],
        'headline_font' => ['type' => 'font', 'label_ar' => 'خط العناوين', 'default' => '', 'group' => 'base'],
        'title_font_size' => ['type' => 'font_size', 'label_ar' => 'حجم العنوان', 'default' => '', 'min' => 10, 'max' => 96, 'group' => 'title', 'group_label_ar' => 'العنوان الرئيسي'],
        'title_font_weight' => ['type' => 'font_weight', 'label_ar' => 'سُمك العنوان', 'default' => '', 'group' => 'title'],
        'title_color' => ['type' => 'optional_color', 'label_ar' => 'لون العنوان', 'default' => '', 'group' => 'title'],
        'label_font_size' => ['type' => 'font_size', 'label_ar' => 'حجم العنوان الفرعي', 'default' => '', 'min' => 8, 'max' => 48, 'group' => 'label', 'group_label_ar' => 'العنوان الفرعي / التسمية'],
        'label_font_weight' => ['type' => 'font_weight', 'label_ar' => 'سُمك العنوان الفرعي', 'default' => '', 'group' => 'label'],
        'label_color' => ['type' => 'optional_color', 'label_ar' => 'لون العنوان الفرعي', 'default' => '', 'group' => 'label'],
        'body_font_size' => ['type' => 'font_size', 'label_ar' => 'حجم الوصف', 'default' => '', 'min' => 8, 'max' => 48, 'group' => 'body', 'group_label_ar' => 'الوصف / النص'],
        'body_font_weight' => ['type' => 'font_weight', 'label_ar' => 'سُمك الوصف', 'default' => '', 'group' => 'body'],
        'body_color' => ['type' => 'optional_color', 'label_ar' => 'لون الوصف', 'default' => '', 'group' => 'body'],
    ],

    /**
     * Icons for event-details cards (builder-wedding-section-details).
     */
    'detail_card_icons' => [
        'calendar' => ['label_ar' => 'تقويم', 'label_en' => 'Calendar', 'glyph' => '📅'],
        'clock' => ['label_ar' => 'ساعة', 'label_en' => 'Clock', 'glyph' => '🕐'],
        'location' => ['label_ar' => 'موقع', 'label_en' => 'Location', 'glyph' => '📍'],
        'reception' => ['label_ar' => 'احتفال', 'label_en' => 'Celebration', 'glyph' => '🎉'],
        'heart' => ['label_ar' => 'قلب', 'label_en' => 'Heart', 'glyph' => '♥'],
        'ring' => ['label_ar' => 'خاتم', 'label_en' => 'Ring', 'glyph' => '💍'],
        'star' => ['label_ar' => 'نجمة', 'label_en' => 'Star', 'glyph' => '★'],
        'gift' => ['label_ar' => 'هدية', 'label_en' => 'Gift', 'glyph' => '🎁'],
        'music' => ['label_ar' => 'موسيقى', 'label_en' => 'Music', 'glyph' => '🎵'],
        'camera' => ['label_ar' => 'كاميرا', 'label_en' => 'Camera', 'glyph' => '📷'],
        'car' => ['label_ar' => 'سيارة', 'label_en' => 'Car', 'glyph' => '🚗'],
        'users' => ['label_ar' => 'ضيوف', 'label_en' => 'Guests', 'glyph' => '👥'],
    ],

    /**
     * Editable fields per information block (stored in settings.block_data).
     * Supported field types: text, textarea, url, email, tel, number, date, time,
     * datetime-local, color, optional_color, font, select, icon_upload, checkbox (repeaters).
     */
    'block_field_schemas' => [
        'countdown' => [
            'fields' => [
                'label' => ['type' => 'text', 'label_ar' => 'عنوان العداد', 'default' => 'العد التنازلي ليوم الحدث'],
                'days_unit' => ['type' => 'text', 'label_ar' => 'وحدة الأيام', 'default' => 'يوم'],
                'hours_unit' => ['type' => 'text', 'label_ar' => 'وحدة الساعات', 'default' => 'ساعة'],
                'mins_unit' => ['type' => 'text', 'label_ar' => 'وحدة الدقائق', 'default' => 'دقيقة'],
                'secs_unit' => ['type' => 'text', 'label_ar' => 'وحدة الثواني', 'default' => 'ثانية'],
            ],
        ],
        'event_details' => [
            'fields' => [
                'label' => ['type' => 'text', 'label_ar' => 'عنوان فرعي', 'default' => 'جميع التفاصيل'],
                'title' => ['type' => 'text', 'label_ar' => 'عنوان القسم', 'default' => 'تفاصيل الحفل'],
            ],
            'groups' => [
                [
                    'label_ar' => 'أيقونات البطاقات',
                    'fields' => [
                        'date_icon' => ['type' => 'select', 'label_ar' => 'أيقونة التاريخ', 'default' => 'calendar', 'options' => 'detail_card_icons'],
                        'date_icon_url' => ['type' => 'icon_upload', 'label_ar' => 'صورة أيقونة التاريخ', 'default' => ''],
                        'ceremony_icon' => ['type' => 'select', 'label_ar' => 'أيقونة الحفل', 'default' => 'clock', 'options' => 'detail_card_icons'],
                        'ceremony_icon_url' => ['type' => 'icon_upload', 'label_ar' => 'صورة أيقونة الحفل', 'default' => ''],
                        'venue_icon' => ['type' => 'select', 'label_ar' => 'أيقونة المكان', 'default' => 'location', 'options' => 'detail_card_icons'],
                        'venue_icon_url' => ['type' => 'icon_upload', 'label_ar' => 'صورة أيقونة المكان', 'default' => ''],
                        'reception_icon' => ['type' => 'select', 'label_ar' => 'أيقونة الاستقبال', 'default' => 'reception', 'options' => 'detail_card_icons'],
                        'reception_icon_url' => ['type' => 'icon_upload', 'label_ar' => 'صورة أيقونة الاستقبال', 'default' => ''],
                    ],
                ],
            ],
        ],
        'venue' => [
            'fields' => [
                'label' => ['type' => 'text', 'label_ar' => 'عنوان فرعي', 'default' => 'موقع الحفل'],
                'title' => ['type' => 'text', 'label_ar' => 'عنوان القسم', 'default' => 'المكان'],
                'description' => ['type' => 'textarea', 'label_ar' => 'وصف المكان', 'default' => ''],
            ],
        ],
        'our_story' => [
            'fields' => [
                'label' => ['type' => 'text', 'label_ar' => 'عنوان فرعي', 'default' => 'كيف بدأت قصتنا'],
                'title' => ['type' => 'text', 'label_ar' => 'عنوان القسم', 'default' => 'قصتنا'],
                'body' => ['type' => 'textarea', 'label_ar' => 'النص الرئيسي', 'default' => ''],
            ],
            'repeaters' => [
                'milestones' => [
                    'label_ar' => 'محطات القصة',
                    'max' => 8,
                    'fields' => [
                        'year' => ['type' => 'date', 'label_ar' => 'التاريخ'],
                        'text' => ['type' => 'textarea', 'label_ar' => 'النص'],
                    ],
                ],
            ],
        ],
        'timeline' => [
            'fields' => [
                'label' => ['type' => 'text', 'label_ar' => 'عنوان فرعي', 'default' => 'يوم الاحتفال'],
                'title' => ['type' => 'text', 'label_ar' => 'عنوان القسم', 'default' => 'الجدول الزمني'],
            ],
            'repeaters' => [
                'items' => [
                    'label_ar' => 'بنود الجدول',
                    'max' => 12,
                    'fields' => [
                        'time' => ['type' => 'time', 'label_ar' => 'الوقت'],
                        'title' => ['type' => 'text', 'label_ar' => 'العنوان'],
                        'place' => ['type' => 'text', 'label_ar' => 'المكان / الوصف'],
                    ],
                ],
            ],
        ],
        'gallery' => [
            'fields' => [
                'label' => ['type' => 'text', 'label_ar' => 'عنوان فرعي', 'default' => 'لحظاتنا'],
                'title' => ['type' => 'text', 'label_ar' => 'عنوان القسم', 'default' => 'معرض الصور'],
            ],
            'repeaters' => [
                'photos' => [
                    'label_ar' => 'الصور',
                    'max' => 8,
                    'fields' => [
                        'url' => ['type' => 'url', 'label_ar' => 'رابط الصورة'],
                        'caption' => ['type' => 'text', 'label_ar' => 'التسمية'],
                        'wide' => ['type' => 'checkbox', 'label_ar' => 'عرض عريض (صفين)'],
                    ],
                ],
            ],
        ],
        'gift_list' => [
            'fields' => [
                'label' => ['type' => 'text', 'label_ar' => 'عنوان فرعي', 'default' => 'إن رغبت بالإهداء'],
                'title' => ['type' => 'text', 'label_ar' => 'عنوان القسم', 'default' => 'قائمة الهدايا'],
                'body' => ['type' => 'textarea', 'label_ar' => 'نص تمهيدي', 'default' => ''],
            ],
            'repeaters' => [
                'items' => [
                    'label_ar' => 'خيارات الهدايا',
                    'max' => 6,
                    'fields' => [
                        'name' => ['type' => 'text', 'label_ar' => 'الاسم'],
                        'subtitle' => ['type' => 'text', 'label_ar' => 'وصف قصير'],
                        'url' => ['type' => 'url', 'label_ar' => 'رابط (اختياري)'],
                    ],
                ],
            ],
        ],
        'menu' => [
            'fields' => [
                'label' => ['type' => 'text', 'label_ar' => 'عنوان فرعي', 'default' => 'قائمة الطعام'],
                'title' => ['type' => 'text', 'label_ar' => 'عنوان القسم', 'default' => 'ماذا نقدّم'],
                'body' => ['type' => 'textarea', 'label_ar' => 'نص تمهيدي', 'default' => ''],
            ],
            'repeaters' => [
                'items' => [
                    'label_ar' => 'الأطباق',
                    'max' => 12,
                    'fields' => [
                        'name' => ['type' => 'text', 'label_ar' => 'اسم الطبق'],
                        'description' => ['type' => 'text', 'label_ar' => 'الوصف'],
                    ],
                ],
            ],
        ],
        'rsvp' => [
            'fields' => [
                'label' => ['type' => 'text', 'label_ar' => 'عنوان فرعي', 'default' => 'يرجى الرد'],
                'title' => ['type' => 'text', 'label_ar' => 'عنوان القسم', 'default' => 'هل ستشاركنا؟'],
                'body' => ['type' => 'textarea', 'label_ar' => 'نص الدعوة للرد', 'default' => ''],
            ],
        ],
        'wishes' => [
            'fields' => [
                'label' => ['type' => 'text', 'label_ar' => 'عنوان فرعي', 'default' => 'تهانيكم'],
                'title' => ['type' => 'text', 'label_ar' => 'عنوان القسم', 'default' => 'رسائل المحبة'],
                'body' => ['type' => 'textarea', 'label_ar' => 'نص تمهيدي', 'default' => ''],
            ],
        ],
        'background_music' => [
            'fields' => [
                'audio_url' => ['type' => 'audio_upload', 'label_ar' => 'ملف الموسيقى', 'default' => ''],
                'volume' => ['type' => 'number', 'label_ar' => 'مستوى الصوت (٪)', 'default' => '50', 'min' => 0, 'max' => 100],
                'loop' => ['type' => 'checkbox', 'label_ar' => 'تكرار الموسيقى', 'default' => true],
            ],
        ],
    ],

    'information_blocks' => [
        'countdown' => ['label_ar' => 'عداد تنازلي', 'icon' => '⏱', 'description_ar' => 'العد التنازلي ليوم الحدث'],
        'event_details' => ['label_ar' => 'بطاقات التفاصيل', 'icon' => '💍', 'description_ar' => 'التاريخ، الوقت، المكان، الاستقبال'],
        'venue' => ['label_ar' => 'المكان', 'icon' => '📍', 'description_ar' => 'عنوان القاعة والخريطة'],
        'timeline' => ['label_ar' => 'الجدول الزمني', 'icon' => '📅', 'description_ar' => 'برنامج اليوم'],
        // 'dress_code' => ['label_ar' => 'قواعد اللباس', 'icon' => '👗', 'description_ar' => 'الزي المطلوب'],
        'gift_list' => ['label_ar' => 'قائمة الهدايا', 'icon' => '🎁', 'description_ar' => 'سجل الهدايا أو التبرعات'],
        'our_story' => ['label_ar' => 'قصتنا', 'icon' => '💕', 'description_ar' => 'قصة العروسين'],
        'gallery' => ['label_ar' => 'معرض صور', 'icon' => '🖼', 'description_ar' => 'صور مميزة'],
        'rsvp' => ['label_ar' => 'تأكيد الحضور', 'icon' => '✉️', 'description_ar' => 'قبول أو رفض الدعوة'],
        // 'parking' => ['label_ar' => 'مواقف السيارات', 'icon' => '🚗', 'description_ar' => 'معلومات المواقف'],
        // 'accommodation' => ['label_ar' => 'الإقامة', 'icon' => '🏨', 'description_ar' => 'فنادق قريبة'],
        // 'faq' => ['label_ar' => 'أسئلة شائعة', 'icon' => '❓', 'description_ar' => 'إجابات للضيوف'],
        // 'contact' => ['label_ar' => 'تواصل', 'icon' => '📞', 'description_ar' => 'أرقام التواصل'],
        'menu' => ['label_ar' => 'قائمة الطعام', 'icon' => '🍽', 'description_ar' => 'أطباق الحفل'],
        // 'transport' => ['label_ar' => 'المواصلات', 'icon' => '🚌', 'description_ar' => 'باصات الضيوف'],
        'wishes' => ['label_ar' => 'تهاني', 'icon' => '💬', 'description_ar' => 'رسائل من الضيوف'],
        'background_music' => ['label_ar' => 'موسيقى خلفية', 'icon' => '🎵', 'description_ar' => 'تشغيل موسيقى عند فتح الظرف'],
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

];