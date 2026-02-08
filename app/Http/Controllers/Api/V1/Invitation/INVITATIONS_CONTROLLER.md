# InvitationsController — API Methods

API controller for managing invitations (events/weddings): creating, updating, listing, managing users/admins/guards, packages, payments, and notifications.

**Namespace:** `App\Http\Controllers\Api\V1\Invitation`  
**Middleware:** `auth:sanctum` (all routes under `invitations` prefix)

---

## Table of Contents

1. [index](#index)
2. [store](#store)
3. [update](#update)
4. [show](#show)
5. [users](#users)
6. [guards](#guards)
7. [admins](#admins)
8. [removeUser](#removeuser)
9. [addUser](#adduser)
10. [updateAdminHostName](#updateadminhostname)
11. [editUser](#edituser)
12. [addAdmin](#addadmin)
13. [updateAdminInvitationCount](#updateadmininvitationcount)
14. [addGuard](#addguard)
15. [sendNotificationToUser](#sendnotificationtouser)
16. [sendSMSToUser](#sendsmstouser)
17. [sendTemplateMessage](#sendtemplatemessage)
18. [updateStatus](#updatestatus)
19. [checkInvitation](#checkinvitation)
20. [PaymentReceipt](#paymentreceipt)
21. [shareInvitation](#shareinvitation)
22. [shareSmsInvitationApp](#sharesmsinvitationapp)
23. [shareInvitationSms](#shareinvitationsms)
24. [completeRequestInvitation](#completerequestinvitation)
25. [addExtraPackages](#addextrapackages)

---

## index

**Route:** `GET /api/v1/invitations`  
**Request:** `GetInvitationRequest` (query: `type`)

Returns a paginated list of invitations filtered by `type`:

| Type | Description |
|------|-------------|
| **1** | Invitations the user is **invited to** (as guest): paid invitations where the user is in `invitation_user`. Grouped by category. |
| **2** | Invitations **owned by** the user: `user_id = auth()->id()`, status Approved or Pending user approval. Grouped by category. |
| **3** | Invitations where the user is **admin**: invitations that have the user in the admins relation. Grouped by category. |

Response: `CategoryResource` collection (categories with nested invitations), paginated.

---

## store

**Route:** `POST /api/v1/invitations`  
**Request:** `InvitationRequest`

Creates a new invitation for the authenticated user. Behavior depends on **invitation type**:

- **Contact Design:** Stores image/video/audio if provided, sends admin notification (invitation request created), status remains pending.
- **User Design:** Stores main image, sets status to Approved, returns packages and single invitation price; sends admin notification (invitation created).
- **App Design:** No extra logic in this switch.

Always returns the new invitation, packages for the invitation type, single invitation price, and success message.

---

## update

**Route:** `POST /api/v1/invitations/update/{invitation}`  
**Request:** `InvitationRequest` (includes `invitation_step`)

Updates an invitation according to the **step**:

| Step | Constant | Behavior |
|------|----------|----------|
| **Choose Package** | `Choose Package` | Validates dynamic package; creates `InvitationPackage` if `package_id` given; optionally uploads receipt image (then sets invitation to Pending Admin Payment and sends admin notification). Returns paginated users. |
| **Invite Users** | `Invite Users` | Syncs users to invitation via `user_id` array; returns admins and guards. |
| **Add Admin** | `Add Admin` | Syncs admins with `admin_id` and `invitation_count`. |
| **Add Guard** | `Add Guard` | Syncs guards (`guard_id`) and/or extra guards (`extra_guard_id`) with roles. |
| **Update Invitation** | `Update Invitation` | Updates invitation fields (name, description, date, time, location, address, event_name, media type, host_name) and optional main image; sends admin notification (final design delivered or invitation modified depending on status). |

Returns success and invitation data.

---

## show

**Route:** `GET /api/v1/invitations/{invitation}`  
**Request:** Path parameter `invitation` (model binding)

Returns a single invitation as `InvitationResource` with category and user. Adds:

- `admin_invitation_count`: remaining invitation count for the current user if they are an admin (pivot count minus used count).
- `host_name`: if the current user is not the owner, the host name from the admin pivot for that user.

---

## users

**Route:** `GET /api/v1/invitations/invited/users/{invitation}`  
**Request:** `GetUserRequest` (query: optional `seen` filter)

Returns invited **users** for the invitation (only those invited by the current user) and aggregate stats:

- `users_count`: total invitation count for users invited by current user.
- `users_delivered`, `users_not_delivered`, `users_not_downloaded_app`, `users_not_attended`: counts by seen status.
- `users_rest_of_package`: remaining invitations from package (including extra paid count).
- `users`: list of users (filtered by `seen` if provided).

---

## guards

**Route:** `GET /api/v1/invitations/invited/guards/{invitation}`

Returns all **guards** (Guard + Extra Guard) for the invitation as `GuardResource` collection.

---

## admins

**Route:** `GET /api/v1/invitations/invited/admins/{invitation}`

Returns **admins** for the invitation and counts:

- `sum_count`: total invitation count allocated to admins.
- `rest_count`: count of users invited by admins (from invitedToUsers).
- `admins`: `AdminResource` collection.

---

## removeUser

**Route:** `POST /api/v1/invitations/user/delete/{invitation}`  
**Request:** `RemoveUserRequest` (`user_id`, `role`)

Detaches a user from the invitation for the given **role** (User, Admin, Guard, etc.). Returns success message.

---

## addUser

**Route:** `POST /api/v1/invitations/add-user/{invitation}`  
**Request:** `StoreUserRequest` (`users` array: name, phone, invitation_count)

Adds or syncs **guests** to the invitation:

- Validates against package limits (owner or admin).
- For each entry: normalizes phone/country code; finds or creates user; syncs to invitation with role User, invitation_count, invited_by, seen, name; generates and stores QR code; if invitation is paid, sends in-app notification; returns invitation link in response.

Returns success and updated invitation users collection.

---

## updateAdminHostName

**Route:** `POST /api/v1/invitations/update-admin-host-name/{invitation}`  
**Request:** `UpdateAdminHostNameRequest` (`host_name`)

Updates the **host_name** in the pivot for the current user when they are an admin on this invitation. Returns success.

---

## editUser

**Route:** `POST /api/v1/invitations/edit-user/{user}`  
**Request:** `UpdateUserRequest` (`invitation_id`, `invitation_count`, `name`, `phone`)

Updates an **invited user**:

- Validates package count (owner or admin).
- Syncs pivot (invitation_count, name); updates user phone/country_code; if invitation is paid, sends notification.

Returns success and updated user resource.

---

## addAdmin

**Route:** `POST /api/v1/invitations/add-admin/{invitation}`  
**Request:** `StoreAdminRequest` (phone, invitation_count, name, host_name)

Adds an **admin** to the invitation (only invitation owner):

- User must exist (by phone/country_code); cannot add self; cannot add same user twice; validates invitation count against package.
- Syncs user as Admin with invitation_count, name, host_name; sends “admin_added” notification; uses DB transaction.

Returns success and admin resource, or error.

---

## updateAdminInvitationCount

**Route:** `POST /api/v1/invitations/{invitation}/update-admin-invitation-count/{admin}`  
**Request:** `UpdateAdminInvitationCountRequest` (`invitation_count`)

Updates the **invitation_count** for a specific admin on the invitation. Caller must be owner or admin. Validates count against package, updates pivot, sends “admin_invitation_count_updated” notification. Returns success and admin resource.

---

## addGuard

**Route:** `POST /api/v1/invitations/add-guard/{invitation}`  
**Request:** `StoreGuardRequest` (phone, name, password, optional `extra`)

Adds a **guard** or **extra guard**:

- Max 2 guards (non-extra) unless “pay first”.
- Finds or creates user by phone; syncs with role Guard or Extra Guard and stores hashed password; sends “guard_added” notification.

Returns success and the added guard resource.

---

## sendNotificationToUser

**Route:** `POST /api/v1/invitations/send-notification/{invitation}`  
**Request:** `SendNotificationToUserRequest` (`message`)

Sends a **WhatsApp** message and in-app notification to each user invited by the current user. Message is built via `buildStyledInvitationMessage`. Returns success or “no users” error.

---

## sendSMSToUser

**Route:** `POST /api/v1/invitations/send-sms/{invitation}`  
**Request:** `SendNotificationToUserRequest` (`message`, optional `use_template`)

Sends **WhatsApp** (template or custom message) to all invitation users; replaces `{user_id}` for personalization. Sends in-app “invitation_notification” to all. Returns success.

---

## sendTemplateMessage

**Route:** `POST /api/v1/invitations/send-template-message/{invitation}`

Sends the **template** invitation message (SMS template) via WhatsApp to all invitation users and sends in-app “invitation_notification”. Returns success or “no users” error.

---

## updateStatus

**Route:** `POST /api/v1/invitations/status/{invitation}`  
**Request:** `UpdateStatusRequest` (e.g. `status`)

Updates invitation attributes (typically **status**). If status is set to Cancelled and invitation is Paid, notifies all invitation users (“invitation_cancelled”). Returns success.

---

## checkInvitation

**Route:** `GET /api/v1/invitations/check/invitation`  
**Request:** `CheckInvitationRequest` (`invitation_id`, `user_id`)

Used by **guards** to validate and **scan** a guest:

- Ensures current user is a guard for the invitation.
- Ensures the given user_id is an invited user; if already scanned, returns “already scanned”.
- Marks user as scanned (`seen` = scanned) and returns success with invitation_count, guest name, and phone.

Returns structured response with status, message, invitation_count, guest_name, guest_phone.

---

## PaymentReceipt

**Route:** `POST /api/v1/invitations/payment/receipt/{invitation}`  
**Request:** `PaymentReceiptRequest` (`image`)

Handles **payment receipt** for an unpaid invitation package:

- Finds an unpaid `InvitationPackage` for the invitation; sets its status to Pending Admin Payment; sets invitation `paid` to Pending Admin Payment; stores receipt image on the invitation package; sends email to admin. Uses DB transaction.

Returns success message.

---

## shareInvitation

**Route:** `GET /api/v1/invitations/share/{invitation}`

Sends **WhatsApp** invitation message to each user (invited by current user) who is in “in app” or “not in the app” seen status. Uses `buildInvitationMessage`; on successful send updates user pivot to “Sent”. Returns success.

---

## shareSmsInvitationApp

**Route:** `GET /api/v1/invitations/share-sms-invitation-app/{invitation}/{user}`

Returns the **share message** (template with link) for a specific user. Used so the app can show or send the message (e.g. SMS or in-app share). Returns success and message body.

---

## shareInvitationSms

**Route:** `GET /api/v1/invitations/share-sms/{invitation}`

Sends **SMS** (via Twilio template) to users who are already in “Sent” status (e.g. after WhatsApp share). Updates pivot to “delivered” on success. Returns sent_count, failed_count, and errors array.

---

## completeRequestInvitation

**Route:** `GET /api/v1/invitations/complete-request-invitation/{invitation}`

Returns data needed to **complete** an unpaid invitation request:

- Invitation, unpaid package details (id, count, price, free_invitations_count), extra count/price, account_number (from AppSetting), and total_price. Used for payment summary before paying.

---

## addExtraPackages

**Route:** `POST /api/v1/invitations/add-extra-package/{invitation}`  
**Request:** `Request` (body: `package_id`, `count`, optional `image`)

Adds an **extra (dynamic) package** to the invitation:

- Fails if there are existing unpaid invitation packages.
- Gets single invitation price from dynamic package; creates `InvitationPackage` with Pending Admin Payment; optionally stores receipt image; sets invitation `paid` to Pending Admin Payment; sends email to admin. Uses DB transaction.

Returns success and list of unpaid packages for the invitation, or error.

---

## Private helpers (not routed)

- **buildInvitationMessage($invitation, $user_id, $templateType)**  
  Builds a localized message (event type, host name, invitation link, app store links) for SMS/WhatsApp.

- **buildStyledInvitationMessage($invitation, $messageBody, $templateType)**  
  Builds a styled template message with event type, host name, custom body, and app links.

---

## Constants used

- **Invitation types:** App Design, Contact Design, User Design  
- **Invitation steps:** Upload Invitation, Choose Package, Invite Users, Add Guard, Add Admin, Add Payment, Update Invitation  
- **Roles:** User, Admin, Guard, Extra Guard  
- **Statuses:** Approved, Pending admin, Pending user approval, Rejected, Cancelled, Finished  
- **Paid statuses:** Paid, Not Paid, Pending Admin Payment, Rejected  
- **Seen statuses:** not in the app, in app, seen, delivered, scanned, Sent, accepted, declined, etc.
