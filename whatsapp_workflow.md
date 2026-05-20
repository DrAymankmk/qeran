# WhatsApp workflow (Baileys)

## Sessions

| Session ID | Phone | Used for |
|------------|-------|----------|
| `system` | Qeran business number | OTP, contact-us, admin replies |
| `user_{id}` | Client's WhatsApp | Bulk invitations from client number |

## A. System number (admin)

1. Admin login → **Settings → ربط واتساب (OTP)** (`/admin/whatsapp-system`)
2. Generate QR → scan with qeran business phone
3. Status = `connected`

## B. OTP (register / reset password)

- `VerificationService` → `BaileysWhatsApp::sendLegacy()` → gateway `system` session

## C. Client link (one phone — pairing code)

**API** (Bearer token, `auth:sanctum`):

| Method | Path | Body |
|--------|------|------|
| `POST` | `/api/v1/whatsapp/connect` | `{ "phone": "9665..." }` optional (defaults to user profile) |
| `GET` | `/api/v1/whatsapp/status` | — |
| `POST` | `/api/v1/whatsapp/disconnect` | — |

**App flow:**

1. User taps Connect WhatsApp
2. App calls `POST /whatsapp/connect`
3. Response includes `pairing_code` (8 chars) + `instructions`
4. Same phone: WhatsApp → Linked devices → Link with phone number → enter code
5. App polls `GET /whatsapp/status` until `connected: true`
6. User can send invitations

## D. Send invitations

- `GET /api/v1/invitations/share/{invitation}` (and related send endpoints)
- Requires client WhatsApp `connected`
- Messages queued: ~12s apart, ~80/day (ban safety)
- Sent from **client number** via `user_{id}` session

## Deploy checklist

**Gateway** (`/www/wwwroot/whatsapp-gateway`):

```bash
npm run build
pm2 restart whatsapp-gateway
curl -s https://wa-gateway.qeran.app/health | jq .   # version 1.2.0+
```

**Laravel** (`/www/wwwroot/qeran`):

```env
BAILEYS_GATEWAY_URL=https://wa-gateway.qeran.app
BAILEYS_GATEWAY_SECRET=...
BAILEYS_SYSTEM_SESSION=system
QUEUE_CONNECTION=redis
```

```bash
php artisan migrate
php artisan config:clear
# Supervisor: php artisan queue:work redis
```
