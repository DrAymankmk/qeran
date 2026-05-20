#!/usr/bin/env bash
# Quick check for B.3 API (run on server after npm run build && pm2 start)
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:3000}"
SECRET="${BAILEYS_GATEWAY_SECRET:-}"

if [[ -z "$SECRET" ]]; then
  echo "Set BAILEYS_GATEWAY_SECRET in environment or .env"
  exit 1
fi

AUTH="Authorization: Bearer $SECRET"

echo "== GET /health =="
curl -s "$BASE_URL/health" | jq .

echo "== POST /sessions (system) =="
curl -s -X POST "$BASE_URL/sessions" \
  -H "$AUTH" -H "Content-Type: application/json" \
  -d '{"sessionId":"system"}' | jq .

echo "== GET /sessions/system/status =="
curl -s "$BASE_URL/sessions/system/status" -H "$AUTH" | jq .

echo "== GET /sessions/system/qr (first 80 chars of qrImage) =="
curl -s "$BASE_URL/sessions/system/qr" -H "$AUTH" | jq '{status, qr: .qr[0:40], qrImage: .qrImage[0:80]}'

echo "Done. Scan QR from qrImage in a browser or implement Phase F in Laravel."
