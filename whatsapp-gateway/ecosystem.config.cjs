/**
 * PM2 config — loads .env from project root.
 * Usage: pm2 start ecosystem.config.cjs
 */
const path = require('path');
require('dotenv').config({ path: path.join(__dirname, '.env') });

module.exports = {
  apps: [
    {
      name: 'whatsapp-gateway',
      script: 'dist/index.js',
      cwd: __dirname,
      env: {
        NODE_ENV: 'production',
        PORT: process.env.PORT || 3000,
        HOST: process.env.HOST || '127.0.0.1',
        BAILEYS_GATEWAY_SECRET: process.env.BAILEYS_GATEWAY_SECRET,
        SESSIONS_DIR: process.env.SESSIONS_DIR || './sessions',
        RECONNECT_WIPE_THRESHOLD: process.env.RECONNECT_WIPE_THRESHOLD ?? '0',
        RECONNECT_BACKOFF_MS: process.env.RECONNECT_BACKOFF_MS ?? '30000,120000,600000',
        CONNECTED_SESSION_WATCHDOG_MS: process.env.CONNECTED_SESSION_WATCHDOG_MS ?? '45000',
        ENABLE_QR_SETUP_PAGE: process.env.ENABLE_QR_SETUP_PAGE ?? 'true',
        LARAVEL_WEBHOOK_URL: process.env.LARAVEL_WEBHOOK_URL || '',
        LARAVEL_WEBHOOK_SECRET: process.env.LARAVEL_WEBHOOK_SECRET || '',
      },
    },
  ],
};
