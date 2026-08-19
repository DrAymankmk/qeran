# Qeran App - API Documentation

Base URL: `/api/v1`

---

## Table of Contents

- [Utility Routes](#utility-routes)
- [Authentication](#authentication)
- [Home & Settings](#home--settings)
- [App Settings](#app-settings)
- [Webhooks](#webhooks)
- [Notifications](#notifications)
- [Profile](#profile)
- [WhatsApp](#whatsapp)
- [Packages](#packages)
- [Invitations](#invitations)

---

## Utility Routes

| Method | Endpoint | Auth |
|--------|----------|------|
| GET | `/api/command` | No |
| GET | `/api/run-optimize` | No |

### `GET /api/command`
Simple health-check endpoint. Returns the string `"Optimization completed"`.

### `GET /api/run-optimize`
Runs a series of Artisan optimization and deployment commands on the server:
- Clears all caches (`optimize:clear`)
- Caches routes, config, views, and events
- Runs database migrations

Returns `"Optimization clear"` on success.

> **Warning:** This endpoint has no authentication. It should be protected or removed in production.

---

## Authentication

All auth routes are prefixed with `/api/v1`. None of the following require a token unless noted.

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/login` | No | User login |
| POST | `/login-guard` | No | Guard login |
| POST | `/register` | No | User registration |
| POST | `/change_password` | No | Change password |
| POST | `/verify` | No | Verify OTP code |
| POST | `/send_code` | No | Send OTP code |
| POST | `/auth/store` | No | Store/create auth record |
| POST | `/auth/update/{category_id}` | No | Update auth record by category |
| POST | `/auth/logout` | Yes (Sanctum) | Logout and revoke token |
| GET | `/auth/delete` | Yes (Sanctum) | Delete user account |
| POST | `/auth/change-language` | Yes (Sanctum) | Change user language preference |
| GET | `/generate-beams-token` | Yes (Sanctum) | Generate Pusher Beams auth token |

### `POST /login`
Authenticates a user with their credentials (phone/email and password). Returns user data and a Sanctum API token.

### `POST /login-guard`
Authenticates a guard user. Guards are a special role used for event/invitation security check-in.

### `POST /register`
Registers a new user account. Expects user details (name, phone, email, password, etc.).

### `POST /change_password`
Allows a user to change their password (typically after verifying OTP).

### `POST /verify`
Verifies an OTP confirmation code sent to the user's phone/email during registration or password reset.

### `POST /send_code`
Sends a verification OTP code to the user via SMS or other delivery channel.

### `POST /auth/store`
Creates a new auth/category record for the user.

### `POST /auth/update/{category_id}`
Updates an existing auth/category record identified by `category_id`.

### `POST /auth/logout`
Logs the user out by revoking their current Sanctum token. Requires authentication.

### `GET /auth/delete`
Permanently deletes the authenticated user's account and associated data.

### `POST /auth/change-language`
Changes the preferred language for the authenticated user.

### `GET /generate-beams-token`
Generates a Pusher Beams authentication token for push notification subscriptions.

---

## Home & Settings

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/home` | No | Home page data |
| GET | `/settings` | No | Get app settings |
| POST | `/contact-us` | No | Submit contact form |

### `GET /home`
Returns the home/landing page data for the app (featured invitations, banners, categories, etc.).

### `GET /settings`
Retrieves general application settings (terms, privacy policy, about, social links, etc.).

### `POST /contact-us`
Submits a contact-us form with the user's name, email, subject, and message.

---

## App Settings

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/app-settings` | No | List all app settings |
| GET | `/app-settings/show` | No | Show a specific setting |
| GET | `/app-settings/by-category` | No | Get settings by category |

### `GET /app-settings`
Returns a list of all configurable app settings.

### `GET /app-settings/show`
Returns details for a specific app setting (pass setting identifier as query parameter).

### `GET /app-settings/by-category`
Returns app settings filtered by a specific category.

---

## Webhooks

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/whatsapp-webhook` | No | WhatsApp incoming webhook |
| POST | `/webhooks/baileys-message-status` | No | Baileys message status webhook |

### `POST /whatsapp-webhook`
Receives incoming WhatsApp webhook events (messages, status updates) from the WhatsApp Business API.

### `POST /webhooks/baileys-message-status`
Receives message delivery status updates from the Baileys WhatsApp library (sent, delivered, read, failed).

---

## Notifications

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/notifications` | No | List notifications |
| PUT | `/notifications/read/{notification}` | No | Mark as read |
| PUT | `/notifications/read-all` | No | Mark all as read |
| GET | `/notifications/delete/{notification}` | No | Delete notification |
| POST | `/notifications/pusher/auth` | No | Pusher auth |
| GET | `/send-notify` | Yes (Sanctum) | Send test notification |

### `GET /notifications`
Returns a paginated list of notifications for the user.

### `PUT /notifications/read/{notification}`
Marks a specific notification as read by its ID.

### `PUT /notifications/read-all`
Marks all notifications as read for the current user.

### `GET /notifications/delete/{notification}`
Deletes a specific notification by its ID.

### `POST /notifications/pusher/auth`
Authenticates a Pusher channel subscription for real-time notifications.

### `GET /send-notify` *(Authenticated)*
A test/debug endpoint that sends a sample push notification to the authenticated user.

---

## Profile

Requires Sanctum authentication.

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/profile` | Yes | Get user profile |
| POST | `/profile/update` | Yes | Update user profile |

### `GET /profile`
Returns the authenticated user's profile information.

### `POST /profile/update`
Updates the authenticated user's profile (name, avatar, phone, etc.).

---

## WhatsApp

Requires Sanctum authentication.

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/whatsapp/connect` | Yes | Connect WhatsApp |
| GET | `/whatsapp/status` | Yes | Check connection status |
| POST | `/whatsapp/disconnect` | Yes | Disconnect WhatsApp |

### `POST /whatsapp/connect`
Initiates a WhatsApp connection for the authenticated user (generates QR code or session).

### `GET /whatsapp/status`
Returns the current WhatsApp connection status for the authenticated user.

### `POST /whatsapp/disconnect`
Disconnects the user's WhatsApp session.

---

## Packages

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/packages/{invitation}` | Yes | Get invitation packages |

### `GET /packages/{invitation}`
Returns available packages for a specific invitation. Packages define features, limits, and pricing tiers.

---

## Invitations

All invitation routes require Sanctum authentication and use the `api.tracker` middleware.

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/invitations` | Yes | List invitations |
| POST | `/invitations/store` | Yes | Create invitation |
| GET | `/invitations/{invitation}` | Yes | Show invitation |
| GET | `/invitations/show-by-id/{id}` | Yes | Show invitation by ID |
| POST | `/invitations/update/{invitation}` | Yes | Update invitation |
| DELETE | `/invitations/delete/{id}` | Yes | Delete invitation |
| POST | `/invitations/add-admin/{invitation}` | Yes | Add admin to invitation |
| POST | `/invitations/add-guard/{invitation}` | Yes | Add guard to invitation |
| POST | `/invitations/add-user/{invitation}` | Yes | Add user/guest to invitation |
| POST | `/invitations/edit-user/{user}` | Yes | Edit invited user |
| POST | `/invitations/{invitation}/update-admin-invitation-count/{admin}` | Yes | Update admin invitation count |
| POST | `/invitations/send-notification/{invitation}` | Yes | Send notification to user |
| POST | `/invitations/update-admin-host-name/{invitation}` | Yes | Update admin host name |
| POST | `/invitations/send-sms/{invitation}` | Yes | Send SMS to user |
| POST | `/invitations/send-template-message/{invitation}` | Yes | Send WhatsApp template message |
| GET | `/invitations/share-sms-invitation-app/{invitation}/{user}` | Yes | Share SMS invitation app link |
| GET | `/invitations/invited/users/{invitation}` | Yes | List invited users |
| GET | `/invitations/invited/admins/{invitation}` | Yes | List invitation admins |
| GET | `/invitations/invited/guards/{invitation}` | Yes | List invitation guards |
| POST | `/invitations/status/{invitation}` | Yes | Update invitation status |
| POST | `/invitations/user/delete/{invitation}` | Yes | Remove user from invitation |
| GET | `/invitations/check/invitation` | Yes | Check invitation validity |
| POST | `/invitations/add-contact/{invitation}` | Yes | Add contact to invitation |
| DELETE | `/invitations/delete-contact/{id}` | Yes | Delete contact |
| POST | `/invitations/share/{invitationId}` | Yes | Share invitation |
| GET | `/invitations/share/{invitation}/contact-logs` | Yes | View contact invitation logs |
| GET | `/invitations/share-sms/{invitation}` | Yes | Share invitation via SMS |
| POST | `/invitations/payment/receipt/{invitation}` | Yes | Submit payment receipt |
| POST | `/invitations/add-extra-package/{invitation}` | Yes | Add extra package |
| GET | `/invitations/complete-request-invitation/{invitation}` | Yes | Complete invitation request |

### `GET /invitations`
Returns a paginated list of invitations belonging to the authenticated user.

### `POST /invitations/store`
Creates a new invitation with details such as title, description, date, location, category, and design.

### `GET /invitations/{invitation}`
Returns full details for a specific invitation by its slug or UUID.

### `GET /invitations/show-by-id/{id}`
Returns full details for a specific invitation by its numeric ID.

### `POST /invitations/update/{invitation}`
Updates an existing invitation's details (title, date, location, design, etc.).

### `DELETE /invitations/delete/{id}`
Permanently deletes an invitation by its ID.

### `POST /invitations/add-admin/{invitation}`
Adds an admin user to the invitation. Admins can manage the invitation and its guest list.

### `POST /invitations/add-guard/{invitation}`
Adds a guard to the invitation. Guards handle check-in at the event venue.

### `POST /invitations/add-user/{invitation}`
Adds a guest/invitee to the invitation with their details (name, phone, companion count, etc.).

### `POST /invitations/edit-user/{user}`
Edits the details of an already-invited user (name, phone, companion count, etc.).

### `POST /invitations/{invitation}/update-admin-invitation-count/{admin}`
Updates the number of invitations allocated to a specific admin for distribution.

### `POST /invitations/send-notification/{invitation}`
Sends a push notification to a specific invited user for the given invitation.

### `POST /invitations/update-admin-host-name/{invitation}`
Updates the host name displayed for an admin on the invitation.

### `POST /invitations/send-sms/{invitation}`
Sends an SMS invitation to a specific user for the given invitation.

### `POST /invitations/send-template-message/{invitation}`
Sends a WhatsApp template message to invited users for the given invitation.

### `GET /invitations/share-sms-invitation-app/{invitation}/{user}`
Generates and returns an SMS-shareable link for a specific user's invitation in the app.

### `GET /invitations/invited/users/{invitation}`
Returns a list of all invited guests/users for a specific invitation.

### `GET /invitations/invited/admins/{invitation}`
Returns a list of all admins assigned to a specific invitation.

### `GET /invitations/invited/guards/{invitation}`
Returns a list of all guards assigned to a specific invitation.

### `POST /invitations/status/{invitation}`
Updates the status of an invitation (e.g., active, paused, completed, cancelled).

### `POST /invitations/user/delete/{invitation}`
Removes a specific user/guest from the invitation.

### `GET /invitations/check/invitation`
Checks if a given invitation code or link is valid and returns basic invitation info.

### `POST /invitations/add-contact/{invitation}`
Adds a contact (from phone contacts) to the invitation's guest list.

### `DELETE /invitations/delete-contact/{id}`
Deletes a contact from the invitation by contact ID.

### `POST /invitations/share/{invitationId}`
Shares the invitation via the platform (generates a share link or sends to contacts).

### `GET /invitations/share/{invitation}/contact-logs`
Returns logs of all contacts the invitation has been shared with and their delivery status.

### `GET /invitations/share-sms/{invitation}`
Generates an SMS share link for the invitation.

### `POST /invitations/payment/receipt/{invitation}`
Submits a payment receipt/proof for an invitation's package purchase.

### `POST /invitations/add-extra-package/{invitation}`
Adds an extra/addon package to an existing invitation (e.g., more guests, additional features).

### `GET /invitations/complete-request-invitation/{invitation}`
Marks the invitation setup as complete and submits it for review/activation.
