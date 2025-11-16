<?php

return [
    'invitation_notification_template' => "🌟Quran App for Sending Invitations🌟\nWith joy and happiness\n :host_name /n We are honored to invite you to attend :event_type \nPlease show the attached code with the invitation at the following link when entering\n\n:invitation_link\n\nFor more information about the Quran app, scan the code (QR code will be loaded from the main admin)",
    
    'invitation_sms_template' => "🌟Quran App for Sending Invitations🌟\nWith joy and happiness\n :host_name \n We are honored to invite you to attend :event_type \nPlease show the attached code with the invitation at the following link when entering\n\n:invitation_link\n\nFor more information about the Quran app, scan the code\n\nThe registered number belongs to the Quran app, and the invitation has been sent to you at the request of Mr./:host_name and under their responsibility.",
    
    'variables' => [
        'event_type' => 'event type',
        'host_name' => 'host name',
        'invitation_link' => 'invitation link',
    ]
];
