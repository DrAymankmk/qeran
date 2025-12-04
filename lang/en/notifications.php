<?php

return [
    // General notifications
    'welcome' => [
        'title' => 'Welcome',
        'body' => 'Welcome to our app, we hope you have a great experience!'
    ],
    
    // Invitation notifications
    'invitation_created' => [
        'title' => 'New Invitation',
        'body' => 'A new invitation has been created successfully'
    ],
    'invitation_updated' => [
        'title' => 'Invitation Updated',
        'body' => 'The invitation has been updated successfully'
    ],
    'invitation_deleted' => [
        'title' => 'Invitation Deleted',
        'body' => 'The invitation has been deleted successfully'
    ],
    'invitation_shared' => [
        'title' => 'Invitation Shared',
        'body' => 'An invitation has been shared with you'
    ],
    'invitation_reminder' => [
        'title' => 'Invitation Reminder',
        'body' => 'Don\'t forget to attend the invitation'
    ],
    
    // User notifications
    'user_registered' => [
        'title' => 'New User Registered',
        'body' => 'A new user has registered in the system'
    ],
    'profile_updated' => [
        'title' => 'Profile Updated',
        'body' => 'Your profile has been updated successfully'
    ],
    'password_changed' => [
        'title' => 'Password Changed',
        'body' => 'Your password has been changed successfully'
    ],
    
    // System notifications
    'system_maintenance' => [
        'title' => 'System Maintenance',
        'body' => 'System maintenance will be performed soon'
    ],
    'system_update' => [
        'title' => 'System Update',
        'body' => 'System has been updated successfully'
    ],
    
    // Admin notifications
    'admin_message' => [
        'title' => 'Admin Message',
        'body' => 'You have a new message from administration'
    ],
    
    // Order/Package notifications
    'order_created' => [
        'title' => 'New Order',
        'body' => 'A new order has been created successfully'
    ],
    'order_updated' => [
        'title' => 'Order Updated',
        'body' => 'Your order has been updated'
    ],
    'order_completed' => [
        'title' => 'Order Completed',
        'body' => 'Your order has been completed successfully'
    ],
    'order_cancelled' => [
        'title' => 'Order Cancelled',
        'body' => 'The order has been cancelled'
    ],
    
    // Payment notifications
    'payment_success' => [
        'title' => 'Payment Successful',
        'body' => 'Payment was successful'
    ],
    'payment_failed' => [
        'title' => 'Payment Failed',
        'body' => 'Payment failed, please try again'
    ],
    
    // Rating notifications
    'rating_received' => [
        'title' => 'New Rating',
        'body' => 'You have been rated by a client'
    ],
    'rating_reminder' => [
        'title' => 'Rating Reminder',
        'body' => 'Don\'t forget to rate the service'
    ],
    
    // Message notifications
    'new_message' => [
        'title' => 'New Message',
        'body' => 'You have a new message'
    ],
    'message_reply' => [
        'title' => 'Message Reply',
        'body' => 'Your message has been replied to'
    ],
    
    // Additional invitation notifications
    'invitation_received' => [
        'title' => 'New Invitation',
        'body' => 'You have a new invitation!'
    ],
    'admin_added' => [
        'title' => 'Added as Admin',
        'body' => 'You are added as an admin to invitation'
    ],
    'admin_invitation_count_updated' => [
        'title' => 'Invitation Count Updated',
        'body' => 'Your invitation count has been updated to :count'
    ],
    'guard_added' => [
        'title' => 'Added as Guard',
        'body' => 'You are added as a guard to an invitation!'
    ],
    'invitation_cancelled' => [
        'title' => 'Invitation Cancelled',
        'body' => 'Invitation is cancelled!'
    ],
    'invitation_notification' => [
        'title' => 'Invitation Notification',
        'body' => 'You have a new notification about the invitation'
    ],
    'invitation_confirmation_request' => [
        'title' => 'Invitation Confirmation Request',
        'body' => 'Please confirm your invitation!'
    ],
    'payment_approved' => [
        'title' => 'Payment Approved',
        'body' => 'Your payment is successfully approved!'
    ],
    
    // Default fallback
    'default' => [
        'title' => 'New Notification',
        'body' => 'You have a new notification'
    ]
]; 