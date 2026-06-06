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

### Delivery / read receipts (contact invitations)

1. Job sends with `referenceId` → gateway stores `idMessage` + `referenceId`
2. Baileys `messages.update` → gateway POSTs to Laravel  
   `POST /api/v1/webhooks/baileys-message-status` (Bearer `BAILEYS_GATEWAY_SECRET`)
3. `invitation_contact_logs`: `delivered_at`, `read_at`, `whatsapp_status` via  
   `GET /api/v1/invitations/share/{invitation}/contact-logs`

Gateway `.env`:

```env
LARAVEL_APP_URL=https://your-qeran-domain.com
# or full URL:
# LARAVEL_RECEIPT_WEBHOOK_URL=https://your-qeran-domain.com/api/v1/webhooks/baileys-message-status
BAILEYS_GATEWAY_SECRET=same-as-laravel
```

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
# Supervisor: php artisan queue:work redis --sleep=3 --tries=3 --timeout=120
```

### Queue troubleshooting (invitation WhatsApp jobs)

`SendBaileysInvitationContactMessage` only runs when a worker is listening on the **same** connection as `QUEUE_CONNECTION`:

| `QUEUE_CONNECTION` | Where jobs live | Worker command |
|---|---|---|
| `redis` | Redis list `queues:default` | `php artisan queue:work redis` |
| `database` | `jobs` table | `php artisan queue:work database` |
| `sync` | (none — runs inline in the HTTP request) | N/A |

If rows sit in `jobs` with `reserved_at` null and `available_at` in the past, the worker is not running or is pointed at the wrong driver.

```bash
# On server — must match .env QUEUE_CONNECTION
php artisan tinker --execute="echo config('queue.default');"

# Redis backlog (when QUEUE_CONNECTION=redis)
redis-cli LLEN queues:default

# Stuck database jobs
mysql -e "SELECT id, queue, available_at, reserved_at, attempts FROM jobs ORDER BY id DESC LIMIT 10;"

# Failed jobs
php artisan queue:failed
```

After deploy: `php artisan config:clear` and restart the supervisor queue worker.
