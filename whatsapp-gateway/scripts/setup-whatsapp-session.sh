#!/usr/bin/env bash
# Link qeran "system" WhatsApp session (OTP / admin sends).
# Usage: cd /www/wwwroot/whatsapp-gateway && ./scripts/setup-whatsapp-session.sh
set -euo pipefail

cd "$(dirname "$0")/.."

if [[ -f .env ]]; then
  set -a
  # shellcheck disable=SC1091
  source .env
  set +a
fi

BASE_URL="${BASE_URL:-https://wa-gateway.qeran.app}"
SESSION_ID="${1:-system}"
SECRET="${BAILEYS_GATEWAY_SECRET:-}"

if [[ -z "$SECRET" ]]; then
  echo "ERROR: BAILEYS_GATEWAY_SECRET is not set in .env"
  exit 1
fi

AUTH="Authorization: Bearer $SECRET"
OUT_DIR="${OUT_DIR:-/tmp}"
QR_HTML="${OUT_DIR}/whatsapp-${SESSION_ID}-qr.html"

echo "== Gateway: $BASE_URL | session: $SESSION_ID =="

HEALTH=$(curl -s "$BASE_URL/health" || true)
echo "== 0) Health check =="
echo "$HEALTH" | jq . 2>/dev/null || echo "$HEALTH"
QR_PAGE_OK=$(echo "$HEALTH" | jq -r '.qrSetupPage // false' 2>/dev/null || echo "false")
if [[ "$QR_PAGE_OK" != "true" ]]; then
  echo ""
  echo "WARNING: Gateway is missing the QR browser page (old build)."
  echo "On the server run:"
  echo "  cd /www/wwwroot/whatsapp-gateway && npm run build"
  echo "  pm2 delete whatsapp-gateway 2>/dev/null; pm2 start ecosystem.config.cjs"
  echo "Then run this script again."
  echo ""
fi

echo "== 1) Start session =="
curl -s -X POST "$BASE_URL/sessions" \
  -H "$AUTH" \
  -H "Content-Type: application/json" \
  -d "{\"sessionId\":\"$SESSION_ID\"}" | jq .

echo ""
echo "== 2) Wait for QR (up to 60s) =="
QR_JSON=""
for i in $(seq 1 30); do
  QR_JSON=$(curl -s "$BASE_URL/sessions/$SESSION_ID/qr" -H "$AUTH")
  STATUS=$(echo "$QR_JSON" | jq -r '.status // empty')
  if [[ "$STATUS" == "connected" ]]; then
    echo "Already connected — no QR needed."
    echo "$QR_JSON" | jq .
    exit 0
  fi
  if echo "$QR_JSON" | jq -e '.qrImage' >/dev/null 2>&1; then
    break
  fi
  echo "  attempt $i/30 — QR not ready yet..."
  sleep 2
done

if ! echo "$QR_JSON" | jq -e '.qrImage' >/dev/null 2>&1; then
  echo "ERROR: Could not get QR. Response:"
  echo "$QR_JSON" | jq . 2>/dev/null || echo "$QR_JSON"
  echo "Try: pm2 logs whatsapp-gateway --lines 40"
  exit 1
fi

QR_IMAGE=$(echo "$QR_JSON" | jq -r '.qrImage')
cat > "$QR_HTML" <<EOF
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>WhatsApp QR — $SESSION_ID</title></head>
<body style="font-family:sans-serif;text-align:center;padding:2rem">
<h1>Scan with WhatsApp</h1>
<p>Session: <b>$SESSION_ID</b></p>
<p>WhatsApp → Linked devices → Link a device</p>
<img src="$QR_IMAGE" width="360" alt="QR"/>
<p><small>QR expires in ~60s. Re-run this script if it fails.</small></p>
</body></html>
EOF

echo ""
echo "QR saved on SERVER disk: $QR_HTML"
echo "(This is NOT a public URL — download via SFTP/aaPanel file manager.)"
echo ""
echo "== SCAN IN BROWSER (easiest) =="
echo "Open this URL on your PC or phone browser, then scan with WhatsApp:"
echo "${BASE_URL}/sessions/${SESSION_ID}/qr/page?token=${SECRET}"
echo "(Do not share this link — it contains your gateway secret.)"
echo ""

echo "== 3) Poll status (scan QR now) =="
for i in $(seq 1 40); do
  STATUS_JSON=$(curl -s "$BASE_URL/sessions/$SESSION_ID/status" -H "$AUTH")
  ST=$(echo "$STATUS_JSON" | jq -r '.status')
  PH=$(echo "$STATUS_JSON" | jq -r '.phone // empty')
  echo "  [$i] status=$ST phone=${PH:-—}"
  if [[ "$ST" == "connected" ]]; then
    echo ""
    echo "SUCCESS — session linked."
    echo "$STATUS_JSON" | jq .
    exit 0
  fi
  sleep 3
done

echo ""
echo "Still not connected. Scan QR from $QR_HTML and run:"
echo "  curl -s \"$BASE_URL/sessions/$SESSION_ID/status\" -H \"$AUTH\" | jq ."
