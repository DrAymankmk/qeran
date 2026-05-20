# HTTP API (B.3) — Laravel contract

Base URL: `http://127.0.0.1:3000` (or `BAILEYS_GATEWAY_URL` from qeran `.env`).

**Auth:** all routes below except `/health` require:

```http
Authorization: Bearer <BAILEYS_GATEWAY_SECRET>
```

---

## `GET /health`

No auth. Liveness check.

**Response `200`:**

```json
{ "ok": true }
```

---

## `POST /sessions`

Start or restore a WhatsApp linked-device session.

**Body (QR — admin / two devices):**

```json
{ "sessionId": "system", "linkMethod": "qr" }
```

**Body (pairing code — mobile app, one device):**

```json
{
  "sessionId": "user_42",
  "phone": "966501234567",
  "linkMethod": "pairing"
}
```

Use `user_{laravelUserId}` for client invitation sends.

**Response `200`:**

```json
{
  "sessionId": "system",
  "status": "starting",
  "phone": null
}
```

`status` values: `starting` | `pending_qr` | `connected` | `disconnected`

---

## `GET /sessions/:id/status`

**Response `200` (known session):**

```json
{
  "sessionId": "system",
  "status": "connected",
  "phone": "9665XXXXXXXX"
}
```

**Response `200` (never started):**

```json
{ "status": "disconnected", "phone": null }
```

---

## `GET /sessions/:id/pairing-code`

Query: `?phone=966501234567` (digits only).

**Response `200`:**

```json
{
  "status": "pending_pairing",
  "pairingCode": "ABCD1234",
  "phone": "966501234567"
}
```

User enters code in WhatsApp → Linked devices → Link with phone number.

---

## `GET /sessions/:id/qr`

Returns QR string and a **base64 PNG** (`qrImage`) for mobile/web UI.

**Response `200` (waiting for scan):**

```json
{
  "status": "pending_qr",
  "qr": "2@....",
  "qrImage": "data:image/png;base64,..."
}
```

**Response `200` (already connected):**

```json
{ "status": "connected", "qr": null, "qrImage": null }
```

**Response `404`:** QR not ready yet — call `POST /sessions` and retry after 2–3 seconds.

---

## `POST /send`

**Body:**

```json
{
  "sessionId": "system",
  "to": "966501234567",
  "message": "لقد تم تسجيل حسابك بنجاح كود التفعيل 4829",
  "referenceId": "optional-tracking-id"
}
```

- `to`: digits only (country code + number, no `+`).
- JID used internally: `{digits}@s.whatsapp.net`.

**Response `200`:**

```json
{
  "sent": true,
  "idMessage": "3EB0....",
  "referenceId": "optional-tracking-id"
}
```

**Response `503`:** session not connected or send failed.

```json
{ "sent": false, "error": "Session \"system\" needs QR scan. GET /sessions/system/qr" }
```

---

## `DELETE /sessions/:id`

Logout WhatsApp and delete auth files under `sessions/{id}/`.

**Response `200`:**

```json
{ "deleted": true, "sessionId": "system" }
```

---

## qeran mapping

| qeran use | sessionId | Endpoint |
|-----------|-----------|----------|
| OTP register / reset | `system` | `POST /send` |
| Client bulk invitations | `user_{auth()->id()}` | `POST /send` |
| Link client WhatsApp | `user_{id}` | `POST /sessions` + `GET .../qr` |
