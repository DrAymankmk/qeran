# Contact Guest Codes — Invitation Contact Logs Flow

This document describes the migration from `invitation_user` guest management to `invitation_contact_logs` with per-guest QR scan codes.

## Overview

Each invited contact is stored in `invitation_contact_logs`. A contact can have multiple guest slots (`invitation_count`). Each slot (primary contact + companions) gets its own scan code and QR image.

## Database Changes

### New columns on `invitation_contact_logs`

| Column | Type | Description |
|--------|------|-------------|
| `invitation_count` | `unsignedSmallInteger` | Total guest slots for this contact (default: 1) |
| `guest_codes` | `json` | Contact + companions with code and scan status |

### `guest_codes` JSON structure

```json
{
  "guests": [
    {
      "slot": 1,
      "code": "181-contact-12-1",
      "name": "Ahmed Ali",
      "is_primary": true,
      "is_scanned": false,
      "scanned_at": null
    },
    {
      "slot": 2,
      "code": "181-contact-12-2",
      "name": "Companion 2",
      "is_primary": false,
      "is_scanned": false,
      "scanned_at": null
    }
  ]
}
```

## QR Code Format

| Type | Payload | QR file |
|------|---------|---------|
| Primary contact (slot 1) | `{invitation_id}-contact-{contact_log_id}-1` | `qr-code/Qr-{payload}.png` |
| Companion (slot N) | `{invitation_id}-contact-{contact_log_id}-{N}` | `qr-code/Qr-{payload}.png` |
| Legacy (backward compat) | `{invitation_id}-contact-{contact_log_id}` | Treated as slot 1 |

## API Changes

### Edit contact / guest count

**Old:** `POST /api/v1/invitations/edit-user/{user}`  
**New:** `POST /api/v1/invitations/edit-contact/{contactLog}`

**Body:**
```json
{
  "invitation_id": 181,
  "name": "Ahmed Ali",
  "phone": "501234567",
  "country_code": "+966",
  "invitation_count": 3
}
```

**Behavior:**
- Updates contact name, phone, and guest slot count
- Rebuilds `guest_codes` while preserving scan state for existing slots
- Regenerates QR PNG files for each guest code

### Scan guest QR (guard check-in)

**Route:** `GET /api/v1/invitations/check/invitation`  
**Auth:** Sanctum (guard token)

**Old body:**
```json
{
  "invitation_id": 181,
  "user_id": 45
}
```

**New body:**
```json
{
  "invitation_id": 181,
  "code": "181-contact-12-2"
}
```

**Success response:**
```json
{
  "status": true,
  "message": "...",
  "invitation_count": 3,
  "scanned_count": 1,
  "remaining_count": 2,
  "guest_name": "Companion 2",
  "guest_phone": "+966501234567",
  "slot": 2,
  "code": "181-contact-12-2",
  "contact_log_id": 12,
  "all_scanned": false
}
```

**Already scanned:**
```json
{
  "status": false,
  "message": "already_scanned",
  "invitation_count": 0
}
```

## Controller Methods

| Method | Purpose |
|--------|---------|
| `editUser` | Edits `InvitationContactLog` record and syncs guest codes |
| `checkInvitation` | Parses scan code, marks guest slot as scanned |
| `syncGuestCodes` | Builds/rebuilds guest_codes JSON |
| `parseGuestScanCode` | Parses QR payload (supports legacy format) |
| `markGuestScanned` | Updates guest slot scan status |
| `ensureGuestQrCodes` | Generates QR PNG per guest slot |

## Quota Helpers

New helpers in `app/Helpers/helpers.php`:

- `checkPackageCountForContactLog($invitation, $count, $contactLogId)`
- `checkPackageCountForAdminContactLog($invitation, $count, $adminId, $type, $contactLogId)`

These count `invitation_contact_logs.invitation_count` instead of `invitation_user.invitation_count`.

## Contact Creation

When contacts are added via `POST /invitations/add-contact/{invitation}`, each stored contact log is initialized with:

- `invitation_count` (default: 1, or from `contacts.*.invitation_count`)
- `guest_codes` with slot 1 for the primary contact
- QR code for slot 1

## Migration Notes

- Old `edit-user/{user}` route is replaced by `edit-contact/{contactLog}`
- Old `checkInvitation` logic using `user_id` + `invitation_user` pivot is commented in the controller for reference
- Legacy QR codes without slot suffix still resolve to slot 1

---

## Share Invitation to Contacts (WhatsApp)

**Route:** `POST /api/v1/invitations/share/{invitationId}`

Queues WhatsApp messages to stored `invitation_contact_logs` for the authenticated host/admin.

### Invitation types

| Type | Constant | Invitation link | QR codes link |
|------|----------|-----------------|---------------|
| Contact Design | `INVITATION_TYPE['Contact Design']` (2) | Builder page: `/invitation/{code}/contact/{id}` | `/invitation/{code}/contact/{id}/qr-codes` |
| User Design | `INVITATION_TYPE['User Design']` (3) | **Uploaded media URL**: `/m/{code}/invitation.{webp\|mp4\|...}` | `/invitation/{code}/contact/{id}/qr-codes` |

**Important:** QR codes are **not** shown on the invitation or builder page. They live on a **separate dedicated page** (`qr-codes` route).

- **Invitation link** → view design (builder page for Contact Design, **direct uploaded image/video URL** for User Design), accept/decline on contact page if opened there
- **QR codes link** → download entry QR for main guest + each companion

### WhatsApp message template

Template key: `messages.invitation_contact_share_template`

Includes:

- Event type and host name
- **`invitation_count`** — total slots (main guest + companions)
- **`invitation_link`** — invitation / builder page (design only)
- **`qr_codes_link`** — dedicated QR download page

Example (Arabic):

```
عدد الدعوات: 3 (الضيف الرئيسي + المرافقين)
رابط الدعوة:
https://qeran.app/invitation/{code}/contact/{id}
رابط أكواد الدخول (QR) لكل ضيف:
https://qeran.app/invitation/{code}/contact/{id}/qr-codes
```

### Share flow (backend)

1. `shareInvitation()` loads contact logs for the invitation
2. `prepareContactLogForShare()` ensures `guest_codes` + QR PNG files exist for every slot
3. `buildContactInvitationMessage()` builds the WhatsApp text with count + **two links**
4. `SendBaileysInvitationContactMessage` job sends:
   - Text message with invitation link + QR codes link
   - Uploaded design media (image/video) for **User Design** invitations when available
   - QR codes are **not** on the invitation page and **not** sent as separate WhatsApp images

### QR codes page

Route: `GET /invitation/{invitation_code}/contact/{contact_log_id}/qr-codes`  
View: `resources/views/invitation/contact-qr-codes.blade.php`

Partial: `resources/views/invitation/partials/qr-guests-section.blade.php`

- One card per guest slot (primary first)
- Download button per QR code
- Link back to invitation page

### Related methods

| Method | Purpose |
|--------|---------|
| `shareInvitation` | Re-share stored contacts via WhatsApp |
| `prepareContactLogForShare` | Sync guest codes + QR files before send |
| `buildContactInvitationMessage` | Message with count + invitation link |
| `queueStoredInvitationContactMessages` | Queue Baileys jobs |
| `guestContactInvitationUrl` | Contact page URL (both invitation types) |
| `guestQrCardsForContactLog` | QR cards for the invitation page |
