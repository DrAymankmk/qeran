# Notifications audit (controllers)

This document lists **all cases found in `app/Http/Controllers/` where the code sends a notification/message** to either:

- **Admins (system/admin dashboard)** via `sendAdminNotification(...)` (creates `notifications` row with `user_id = null`, triggers Pusher on `admin-notifications`, and can email `MAIL_TO_ADDRESS`).
- **Users (real user accounts)** via `Notification::notify('users', ...)` or `PushNotificationService::notify(...)` (creates per-user notifications and may trigger push/pusher depending on implementation).
- **WhatsApp/SMS** via `TwilioWhatsApp::send(...)` / `TwilioSMS::sendWithTemplate(...)` (outbound messages; not in-app notifications).

> Locations below are **file + line range** (as seen in the current codebase).

---

## Admin (system) notifications

### Invitation request created (Contact Design)
- **Recipient**: Admin dashboard (system admins)
- **Channel**: Admin notification (DB + Pusher + optional email)
- **Key**: `invitation_request_created`
- **When**: User creates invitation with type `Contact Design` (typically requires admin review)
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L179–L211

### Invitation created (User Design)
- **Recipient**: Admin dashboard (system admins)
- **Channel**: Admin notification (DB + Pusher + optional email)
- **Key**: `invitation_created`
- **When**: User creates invitation with type `User Design` (auto approved, but still notifies admins)
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L245–L278

### Payment receipt uploaded / package chosen (pending admin approval)
- **Recipient**: Admin dashboard (system admins)
- **Channel**: Admin notification (DB + Pusher + optional email)
- **Key**: `package_chosen`
- **When**: During update step “Choose Package”, user uploads receipt image and invitation becomes `Pending Admin Payment`
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L381–L437

### Invitation updated (final design delivered)
- **Recipient**: Admin dashboard (system admins)
- **Channel**: Admin notification (DB + Pusher + optional email)
- **Key**: `final_design_delivered`
- **When**: During update step “Update Invitation” and status is set to `Approved`
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L504–L541

### Invitation updated (modified / pending user approval)
- **Recipient**: Admin dashboard (system admins)
- **Channel**: Admin notification (DB + Pusher + optional email)
- **Key**: `invitation_modified`
- **When**: During update step “Update Invitation” and status is NOT `Approved` (e.g. pending user approval)
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L541–L577

### New Contact Us message (API)
- **Recipient**: Admin dashboard (system admins)
- **Channel**: Admin notification (DB + Pusher + optional email)
- **Key**: `new_message_contact_us`
- **When**: Mobile/API “Contact Us” message is created
- **Location**: `app/Http/Controllers/Api/V1/Settings/SetContactUs.php` L25–L58

### New Contact Us message (Frontend)
- **Recipient**: Admin dashboard (system admins)
- **Channel**: Admin notification (DB + Pusher + optional email)
- **Key**: `new_message_contact_us`
- **When**: Website “Contact Us” form is submitted
- **Location**: `app/Http/Controllers/Frontend/ContactController.php` L59–L90

### New user registered
- **Recipient**: Admin dashboard (system admins)
- **Channel**: Admin notification (DB + Pusher + optional email)
- **Key**: `user_registered`
- **When**: User registers via API auth controller
- **Location**: `app/Http/Controllers/Api/V1/Auth/AuthController.php` L197–L218

### User deleted their account
- **Recipient**: Admin dashboard (system admins)
- **Channel**: Admin notification (DB + Pusher + optional email)
- **Key**: `user_deleted`
- **When**: User deletes account via API auth controller
- **Location**: `app/Http/Controllers/Api/V1/Auth/AuthController.php` L318–L347

---

## User (in-app) notifications

### Invitation received (when a guest is added and invitation is paid)
- **Recipient**: The invited guest user account (`$user->id`)
- **Channel**: User notification (`Notification::notify`)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Invitations']`
- **Key**: `invitation_received`
- **When**: Adding invitation users in bulk; only triggers if `invitation.paid == Paid`
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L771–L777

### Invitation received (when a guest is edited and invitation is paid)
- **Recipient**: The edited guest user account (`$user->id`)
- **Channel**: User notification (`Notification::notify`)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Invitations']`
- **Key**: `invitation_received`
- **When**: Admin/user edits guest details or invitation_count; only triggers if `invitation.paid == Paid`
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L825–L832

### Admin added to an invitation (invitation-level admin role)
- **Recipient**: The user being added as an invitation admin (`$user->id`)
- **Channel**: User notification (`Notification::notify`)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Updated Invitations']`
- **Key**: `admin_added`
- **When**: Invitation owner adds another user as admin in the invitation pivot
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L881–L900

### Admin invitation_count updated (invitation-level admin role)
- **Recipient**: The invitation-admin whose count was updated (`$admin->id`)
- **Channel**: User notification (`Notification::notify`)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Updated Invitations']`
- **Key**: `admin_invitation_count_updated`
- **When**: Owner/admin updates an invitation-admin quota
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L955–L963

### Guard added to an invitation
- **Recipient**: The guard user account (`$guard->id`)
- **Channel**: User notification (`Notification::notify`)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Invitations']`
- **Key**: `guard_added`
- **When**: Adding a guard (or extra guard) to an invitation
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L1011–L1016

### Invitation notification (after sending WhatsApp/SMS to all invitation users)
- **Recipient**: All users in the invitation (`$invitation->users()->pluck('users.id')`)
- **Channel**: User notification (`Notification::notify`)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Invitations']`
- **Key**: `invitation_notification`
- **When**: After `sendSMSToUser(...)` sends WhatsApp messages to users
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L1110–L1117

### Invitation notification (template message path)
- **Recipient**: All users in the invitation (`$invitation->users()->pluck('users.id')`)
- **Channel**: User notification (`Notification::notify`)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Invitations']`
- **Key**: `invitation_notification`
- **When**: After `sendTemplateMessage(...)` sends WhatsApp messages to users using the SMS template
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L1137–L1144

### Invitation cancelled
- **Recipient**: Each user in the invitation (`foreach ($invitation->users as $user)`)
- **Channel**: User notification (`Notification::notify`)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Invitations']`
- **Key**: `invitation_cancelled`
- **When**: `updateStatus(...)` sets invitation status to `Cancelled` while `paid == Paid`
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L1152–L1161

### Invitation confirmation request (admin panel triggers user confirmation)
- **Recipient**: Invitation owner user (`$invitation->user_id`)
- **Channel**: User notification (`Notification::notify`)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Invitation Request']`
- **Key**: `invitation_confirmation_request`
- **When**: Admin updates invitation and sets status `Pending user approval`
- **Location**: `app/Http/Controllers/Admin/InvitationsController.php` L451–L468

### Admin marks invitation paid: notify all invitation users + notify owner payment approved
- **Recipient A**: All invitation users (`$invitation->users->pluck('id')`) → `invitation_received`
- **Recipient B**: Invitation owner (`$invitation->user_id`) → `payment_approved`
- **Channel**: User notification (`Notification::notify`)
- **When**: Admin toggles invitation `paid` status to `Paid`
- **Locations**:
  - `app/Http/Controllers/Admin/InvitationsController.php` L651–L669 (`invitation_received`)
  - `app/Http/Controllers/Admin/InvitationsController.php` L671–L682 (`payment_approved`)

### Admin updates package status: payment approved / rejected
- **Recipient**: Invitation owner (`$invitationPackage->invitation->user_id`)
- **Channel**: User notification (`Notification::notify`)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Invitations']`
- **Keys**:
  - `payment_approved` (when package status becomes Paid)
  - `payment_rejected` (when package status becomes Rejected)
- **When**: Admin changes `invitation_package.status`
- **Location**: `app/Http/Controllers/Admin/InvitationsController.php` L772–L797

### Admin user-management status change (ban/unban or verify)
- **Recipient**: The affected user account (`$user->id`)
- **Channel**: User notification (`Notification::notify`) with `useTranslation = false` (plain strings)
- **Type**: `Constant::NOTIFICATIONS_TYPE['Admin']`
- **Messages**:
  - Title: `Modern Invitation`, Body: `You are blocked by admin!`
  - Title: `Modern Invitation`, Body: `Your account has been verified!`
- **When**: Admin changes user `verified` status (suspended or verified)
- **Location**: `app/Http/Controllers/Admin/UsersController.php` L113–L135

### Contact Us reply (push to contact owner user)
- **Recipient**: The contact’s `user_id` (if contact is linked to a user account)
- **Channel**: Push notification via `PushNotificationService::notify(...)`
- **Type**: `Constant::NOTIFICATIONS_TYPE['Admin']`
- **Category/Type**: `Contact Us` / `New Message`
- **When**: Admin replies to a Contact Us conversation
- **Location**: `app/Http/Controllers/Admin/ContactsController.php` L123–L136

---

## WhatsApp/SMS messages (outbound)

These are **messages sent through Twilio**, not in-app notifications. Many flows also send an in-app notification after messaging (see above).

### Contact Us auto-reply (API)
- **Recipient**: Contact phone number submitted
- **Channel**: WhatsApp (`TwilioWhatsApp::send`)
- **When**: Immediately after creating a Contact Us message
- **Location**: `app/Http/Controllers/Api/V1/Settings/SetContactUs.php` L60–L76

### Contact Us auto-reply (Frontend)
- **Recipient**: Contact phone number submitted
- **Channel**: WhatsApp (`TwilioWhatsApp::send`)
- **When**: Immediately after creating a Contact Us message
- **Location**: `app/Http/Controllers/Frontend/ContactController.php` L91–L107

### Send custom WhatsApp message to invited users
- **Recipient**: Invitation users invited by the authenticated inviter
- **Channel**: WhatsApp (`TwilioWhatsApp::send`)
- **When**: `sendNotificationToUser(...)`
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L1071–L1088

### Send WhatsApp message to all invitation users (custom or template)
- **Recipient**: All users in the invitation
- **Channel**: WhatsApp (`TwilioWhatsApp::send`)
- **When**: `sendSMSToUser(...)` loops `$invitation->users`
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L1090–L1118

### Send template WhatsApp message to all invitation users
- **Recipient**: All users in the invitation
- **Channel**: WhatsApp (`TwilioWhatsApp::send`)
- **When**: `sendTemplateMessage(...)`
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L1125–L1148

### Share invitation via WhatsApp (only users in app / not in app and invited_by current user)
- **Recipient**: Filtered invitation users
- **Channel**: WhatsApp (`TwilioWhatsApp::send`)
- **When**: `shareInvitation(...)`
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L1232–L1247

### Share invitation via SMS provider template
- **Recipient**: Users with `seen == Sent` and invited by current user
- **Channel**: SMS (`TwilioSMS::sendWithTemplate`)
- **When**: `shareInvitationSms(...)`
- **Location**: `app/Http/Controllers/Api/V1/Invitation/InvitationsController.php` L1265–L1299

---

## Non-production / utility controllers

### Example notification controller (reference usage)
- **Purpose**: Demonstrates how to call `sendNotificationAndEmail(...)`
- **Location**: `app/Http/Controllers/ExampleNotificationController.php` L10–L93

### Pusher test controller
- **Purpose**: Sends a test event to `admin-notifications` channel
- **Location**: `app/Http/Controllers/Admin/TestPusherController.php` L13–L97

