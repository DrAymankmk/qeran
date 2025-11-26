<?php

namespace App\Helpers;

class Constant
{
    const USER_STATUS = [
        'Verified' => 1,
        'Not verified' => 2,
        'Suspended' => 3,
    ];

    const USER_TYPE = [
        'User' => 1,
        'Admin' => 2,
    ];

    const USER_GENDER = [
        'Male' => 1,
        'Female' => 2,
    ];

    const REGISTER_TYPE = [
        'By App' => 1,
        'Added By User' => 2,
    ];

    const VERIFICATION_USED = [
        'Used' => 1,
        'Not used' => 2,
    ];

    const VERIFICATION_OBJECTIVE = [
        'Verify' => 1,
        'Reset' => 2,
        'Verify Whatsapp' => 3,
        'Verify Ads' => 4,

    ];

    const VERIFICATION_INFORMATION_TYPE = [
        'Email' => 1,
        'Phone' => 2,
    ];

    const SETTINGS_KEY = [
        'Terms' => 1,
        'About' => 2,
        'Contact' => 3,
    ];

    const VERIFICATION_STATUS = [
        'Verified' => 1,
        'Not verified' => 2,
    ];

    const CATEGORY_STATUS = [
        'Active' => 1,
        'Not active' => 2,
    ];

    const STATUS = [
        'Active' => 1,
        'Not active' => 2,
    ];

    const CATEGORY_TYPE = [
        'Event' => 1,
        'Wedding' => 2,
        'Party' => 3,
    ];

    const FILE_TYPE = [
        'Image' => 1,
        'Video' => 2,
        'Audio' => 3,
        'Gif' => 4,
    ];

    const FILE_KEY = [
        'Main' => 1,
        'Not Main' => 2,
        'Receipt' => 3,
    ];

    const INVITATION_TYPE = [
        'App Design' => 1,
        'Contact Design' => 2,
        'User Design' => 3,
    ];

    const INVITATION_STEP = [
        'Upload Invitation' => 1,
        'Choose Package' => 2,
        'Invite Users' => 3,
        'Add Guard' => 4,
        'Add Admin' => 5,
        'Add Payment' => 6,
        'Update Invitation' => 7,
    ];

    const INVITATION_USER_ROLE = [
        'User' => 1,
        'Admin' => 2,
        'Guard' => 3,
        'Extra Guard' => 4,
    ];

    const INVITATION_STATUS = [
        'Approved' => 1,
        'Pending admin' => 2,
        'Pending user approval' => 3,
        'Rejected' => 4,
        'Cancelled' => -1,
        'Finished Invitation' => 5,
    ];

    const PAID_STATUS = [
        'Paid' => 1,
        'Not Paid' => 2,
        'Pending Admin Payment' => 3,
    ];

    const SEEN_STATUS = [
        'not in the app' => 0,
        'in app' => 1,
        'seen' => 2,
        'delivered' => 3,
        'scanned' => 4,
        'all not attended' => 5,
        'Sent' => 6,
        'accepted' => 7,
        'declined' => 8,
        'did not attend' => 9,
    ];

    const PACKAGE_TYPE = [
        'Static Package' => 1,
        'Dynamic Package' => 2,
    ];

    const PACKAGE_STATUS = [
        'Active' => 1,
        'Not Active' => 2,
    ];

    const CONTACT_US_TYPE = [
        'Contact' => 1,
        'Newsletter' => 2,
        'Suggestion' => 3,
    ];

    const NOTIFICATIONS_TYPE = [
        'Admin' => 0,
        'Invitations' => 1,
        'Updated Invitations' => 2,
        'Invitation Request' => 3,
    ];

    const USER_IMAGE_FOLDER_NAME = 'users';

    const ADMIN_IMAGE_FOLDER_NAME = 'admins';

    const CATEGORY_IMAGE_FOLDER_NAME = 'categories';

    const INVITATION_IMAGE_FOLDER_NAME = 'invitations/images';

    const INVITATION_MAIN_IMAGE_FOLDER_NAME = 'invitations/main_images';

    const INVITATION_VIDEO_FOLDER_NAME = 'invitations/video';

    const INVITATION_AUDIO_FOLDER_NAME = 'invitations/audio';

    const INVITATION_RECEIPT_FOLDER_NAME = 'invitations/receipts';
}
