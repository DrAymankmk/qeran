/**
 * WhatsApp returns 405 (client_too_old) when Baileys sends Platform.WEB for new pairings.
 * Patch validate-connection.js to use Platform.MACOS (see WhiskeySockets/Baileys#2364).
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const target = path.join(
  __dirname,
  '..',
  'node_modules',
  '@whiskeysockets',
  'baileys',
  'lib',
  'Utils',
  'validate-connection.js'
);

if (!fs.existsSync(target)) {
  console.warn('[patch-baileys-405] Baileys not installed — skipping');
  process.exit(0);
}

let content = fs.readFileSync(target, 'utf8');

if (content.includes('PATCHED_MACOS_405')) {
  console.log('[patch-baileys-405] already patched');
  process.exit(0);
}

const before = content;
content = content.replace(
  ': proto.ClientPayload.UserAgent.Platform.WEB,',
  ': proto.ClientPayload.UserAgent.Platform.MACOS, // PATCHED_MACOS_405'
);

if (content === before) {
  console.warn('[patch-baileys-405] Platform.WEB not found — Baileys layout may have changed');
  process.exit(0);
}

fs.writeFileSync(target, content);
console.log('[patch-baileys-405] patched validate-connection.js: WEB -> MACOS');
