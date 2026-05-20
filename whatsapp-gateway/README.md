# qeran WhatsApp gateway (Baileys)

Node service that Laravel (`qeran`) calls to send WhatsApp messages and manage linked-device sessions.

**This folder is the source of those files** referenced in [docs/baileys/baileys_setup_implementation_guide.md](../docs/baileys/baileys_setup_implementation_guide.md) (`src/index.ts`, `src/baileys/manager.ts`, etc.).

## Deploy on server

```bash
# Copy or git pull into e.g. /www/wwwroot/whatsapp-gateway
cd /www/wwwroot/whatsapp-gateway
cp .env.example .env
# Edit .env — set BAILEYS_GATEWAY_SECRET (same value as qeran .env)

npm install
npm run build
pm2 start ecosystem.config.cjs
pm2 save

# After code updates:
# pm2 restart whatsapp-gateway --update-env

# Verify new build (must show qrSetupPage: true):
# curl -s https://wa-gateway.qeran.app/health | jq .
```

## First-time: link qeran system number (OTP)

**Easiest — run the setup script** (does POST + wait for QR + saves HTML + polls status):

```bash
cd /www/wwwroot/whatsapp-gateway
chmod +x scripts/setup-whatsapp-session.sh
./scripts/setup-whatsapp-session.sh
# Opens QR in /tmp/whatsapp-system-qr.html — download and scan
```

Manual curls (POST alone only prints `"status":"starting"` — you must also call `/qr`):

```bash
export $(grep -v '^#' .env | xargs)
curl -X POST "$BASE_URL/sessions" -H "Authorization: Bearer $BAILEYS_GATEWAY_SECRET" \
  -H "Content-Type: application/json" -d '{"sessionId":"system"}'
curl -s "$BASE_URL/sessions/system/qr" -H "Authorization: Bearer $BAILEYS_GATEWAY_SECRET" | jq .
```

Scan with WhatsApp → Linked devices.

**Note:** QR appears **5–30 seconds** after `POST /sessions`. Either wait and call `GET .../qr` again, or use a single `GET .../qr` (latest code waits up to 60s automatically).

If QR never appears, reset the session:

```bash
curl -X DELETE "https://wa-gateway.qeran.app/sessions/system" \
  -H "Authorization: Bearer $BAILEYS_GATEWAY_SECRET"
```

## B.3 / B.4 status

- **B.3 HTTP API** — implemented in `src/index.ts` ([docs/API.md](docs/API.md))
- **B.4 Baileys manager** — implemented in `src/baileys/manager.ts`, `send.ts`, `events.ts`

Test: `bash scripts/test-api.sh` (after `.env` is configured and service is running).

## API (all require `Authorization: Bearer <secret>`)

| Method | Path | Description |
|--------|------|-------------|
| GET | `/health` | No auth — liveness check |
| POST | `/sessions` | `{ "sessionId": "system" \| "user_42" }` |
| GET | `/sessions/:id/status` | Connection status |
| GET | `/sessions/:id/qr` | `{ qr, qrImage }` base64 PNG |
| POST | `/send` | `{ "sessionId", "to", "message" }` |
| DELETE | `/sessions/:id` | Logout and delete auth files |

## qeran `.env`

```env
BAILEYS_GATEWAY_URL=http://127.0.0.1:3000
BAILEYS_GATEWAY_SECRET=same-as-gateway-.env
BAILEYS_SYSTEM_SESSION=system
```
