<?php

return [
    // General notifications
    'welcome' => [
        'title' => 'مرحباً بك',
        'body' => 'مرحباً بك في تطبيقنا، نتمنى لك تجربة رائعة!'
    ],

    // Invitation notifications
    'invitation_created' => [
        'title' => 'دعوة جديدة',
        'body' => 'تم إنشاء دعوة جديدة بنجاح'
    ],
    'invitation_request_created' => [
        'title' => 'طلب دعوة جديد',
        'body' => 'تم إنشاء طلب دعوة جديد بنجاح'
    ],
    'invitation_updated' => [
        'title' => 'تحديث الدعوة',
        'body' => 'تم تحديث الدعوة بنجاح'
    ],
    'invitation_modified' => [
        'title' => 'تعديل الدعوة',
        'body' => 'تم تعديل الدعوة بنجاح'
    ],
    'final_design_delivered' => [
        'title' => 'تم تسليم التصميم النهائي',
        'body' => 'تم تسليم التصميم النهائي بنجاح'
    ],
    'invitation_deleted' => [
        'title' => 'حذف الدعوة',
        'body' => 'تم حذف الدعوة بنجاح'
    ],
    'invitation_shared' => [
        'title' => 'مشاركة الدعوة',
        'body' => 'تم مشاركة الدعوة معك'
    ],
    'invitation_reminder' => [
        'title' => 'تذكير بالدعوة',
        'body' => 'لا تنسى حضور الدعوة'
    ],
    'package_chosen' => [
        'title' => 'تم اختيار الباقة',
        'body' => 'تم اختيار الباقة بنجاح'
    ],

    // User notifications
    'user_registered' => [
        'title' => 'مستخدم جديد مسجل',
        'body' => 'تم تسجيل مستخدم جديد في النظام'
    ],
    'profile_updated' => [
        'title' => 'تحديث الملف الشخصي',
        'body' => 'تم تحديث ملفك الشخصي بنجاح'
    ],
    'password_changed' => [
        'title' => 'تغيير كلمة المرور',
        'body' => 'تم تغيير كلمة المرور بنجاح'
    ],

    // System notifications
    'system_maintenance' => [
        'title' => 'صيانة النظام',
        'body' => 'سيتم إجراء صيانة للنظام قريباً'
    ],
    'system_update' => [
        'title' => 'تحديث النظام',
        'body' => 'تم تحديث النظام بنجاح'
    ],

    // Admin notifications
    'admin_message' => [
        'title' => 'رسالة من الإدارة',
        'body' => 'لديك رسالة جديدة من الإدارة'
    ],

    // Order/Package notifications
    'order_created' => [
        'title' => 'طلب جديد',
        'body' => 'تم إنشاء طلب جديد بنجاح'
    ],
    'order_updated' => [
        'title' => 'تحديث الطلب',
        'body' => 'تم تحديث طلبك'
    ],
    'order_completed' => [
        'title' => 'اكتمال الطلب',
        'body' => 'تم اكتمال طلبك بنجاح'
    ],
    'order_cancelled' => [
        'title' => 'إلغاء الطلب',
        'body' => 'تم إلغاء الطلب'
    ],

    // Payment notifications
    'payment_success' => [
        'title' => 'نجح الدفع',
        'body' => 'تم الدفع بنجاح'
    ],
    'payment_failed' => [
        'title' => 'فشل الدفع',
        'body' => 'فشل في عملية الدفع، يرجى المحاولة مرة أخرى'
    ],

    // Rating notifications
    'rating_received' => [
        'title' => 'تقييم جديد',
        'body' => 'تم تقييمك من قبل عميل'
    ],
    'rating_reminder' => [
        'title' => 'تذكير بالتقييم',
        'body' => 'لا تنسى تقييم الخدمة'
    ],

    // Message notifications
    'new_message' => [
        'title' => 'رسالة جديدة',
        'body' => 'لديك رسالة جديدة'
    ],
    'message_reply' => [
        'title' => 'رد على الرسالة',
        'body' => 'تم الرد على رسالتك'
    ],

    // Additional invitation notifications
    'invitation_received' => [
        'title' => 'دعوة جديدة',
        'body' => 'لديك دعوة جديدة!'
    ],
    'admin_added' => [
        'title' => 'تم إضافتك كمسؤول',
        'body' => 'تم إضافتك كمسؤول في الدعوة'
    ],
    'admin_invitation_count_updated' => [
        'title' => 'تحديث عدد الدعوات',
        'body' => 'تم تحديث عدد دعواتك إلى :count'
    ],
    'guard_added' => [
        'title' => 'تم إضافتك كحارس',
        'body' => 'تم إضافتك كحارس في الدعوة!'
    ],
    'invitation_cancelled' => [
        'title' => 'إلغاء الدعوة',
        'body' => 'تم إلغاء الدعوة!'
    ],
    'invitation_notification' => [
        'title' => 'إشعار الدعوة',
        'body' => 'لديك إشعار جديد عن الدعوة'
    ],
    'invitation_confirmation_request' => [
        'title' => 'طلب تأكيد الدعوة',
        'body' => 'يرجى تأكيد دعوتك!'
    ],
    'payment_approved' => [
        'title' => 'تم الموافقة على الدفع',
        'body' => 'تم الموافقة على دفعتك بنجاح!'
    ],

    'new_message_contact_us' => [
        'title' => 'رسالة جديدة من التواصل',
        'body' => 'لديك رسالة جديدة من التواصل'
    ],

    // Default fallback
    'default' => [
        'title' => 'إشعار جديد',
        'body' => 'لديك إشعار جديد'
    ]
];