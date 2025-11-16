# 🔒 SECURITY CLEANUP CHECKLIST

## ⚠️ CRITICAL: Your server has been compromised with a phishing kit

### 📋 IMMEDIATE ACTIONS REQUIRED

#### 1. **DELETE MALICIOUS FILES** (Do this FIRST)
```bash
# Remove phishing kit
rm -rf public/app/IL/

# Remove bot protection system (if not needed)
rm -rf public/bots/

# Remove admin panel (if not needed)
rm -rf public/Happy/

# Remove any Exec.ini files
find . -name "Exec.ini" -type f -delete
```

#### 2. **REVOKE EXPOSED TELEGRAM BOT TOKEN**
- Bot Token: `7379596250:AAGFP0OSotWJK9U9McBEjnRn51M5JJJf0AY`
- Chat ID: `@KabbirouAllahuAkbar7oct2023`
- **Action**: Go to Telegram BotFather and revoke this token immediately
- **URL**: https://t.me/BotFather
- Send command: `/revoke` or `/deletebot`

#### 3. **CHECK FOR STOLEN DATA**
- Location: `public/app/IL/submissions/` (contains stolen credentials)
- **Action**: Review what data was stolen, notify affected users if any
- **Note**: The submission file shows credit cards, emails, passwords, SMS codes

#### 4. **CHANGE ALL PASSWORDS**
- [ ] Server root password
- [ ] Database passwords
- [ ] Admin panel passwords
- [ ] SSH keys
- [ ] FTP/SFTP credentials
- [ ] Any service account passwords

#### 5. **SECURITY AUDIT**
- [ ] Check server access logs for unauthorized access
- [ ] Review file modification dates
- [ ] Check for other backdoors
- [ ] Scan for suspicious cron jobs
- [ ] Review .htaccess files
- [ ] Check for unauthorized database modifications

#### 6. **SERVER HARDENING**
- [ ] Update all software (PHP, Apache/Nginx, etc.)
- [ ] Review file permissions (should be 644 for files, 755 for directories)
- [ ] Remove unnecessary write permissions
- [ ] Enable firewall rules
- [ ] Review and restrict file upload capabilities
- [ ] Enable security headers

#### 7. **MONITORING**
- [ ] Set up file integrity monitoring
- [ ] Enable intrusion detection
- [ ] Monitor for suspicious network activity
- [ ] Set up alerts for unauthorized file changes

#### 8. **BACKUP & RECOVERY**
- [ ] Create clean backup of legitimate code
- [ ] Document what was removed
- [ ] Plan recovery procedures

### 📁 FILES TO DELETE

#### Phishing Kit (public/app/IL/)
- [ ] common.php
- [ ] config.php (contains Telegram bot token)
- [ ] mail.php (phishing page)
- [ ] login.php
- [ ] sms.php
- [ ] control.php (admin panel)
- [ ] P-control.php
- [ ] load.php
- [ ] approve.php
- [ ] done.php
- [ ] error.php
- [ ] index.php
- [ ] log.php
- [ ] ping.php
- [ ] update_status.php
- [ ] get_user_status.php
- [ ] Entire `submissions/` directory (contains stolen data)
- [ ] Entire `users/` directory
- [ ] Entire `blocked/` directory
- [ ] Entire `custom_fields/` directory
- [ ] honeypotbots.dat

#### Bot Protection System (public/bots/)
- [ ] botMother.php (4,390 lines - very aggressive bot blocking)

#### Admin Panel (public/Happy/)
- [ ] settings.php
- [ ] login.php
- [ ] antibot.php
- [ ] index.php
- [ ] script/login.php
- [ ] script/settings.php
- [ ] script/reset.php
- [ ] script/logout.php

#### Configuration Files
- [ ] Exec.ini (if exists in public/ or root)

### 🔍 ADDITIONAL CHECKS

1. **Search for other suspicious files:**
   ```bash
   # Find PHP files with suspicious patterns
   grep -r "eval\|base64_decode\|gzinflate" public/
   grep -r "telegram\|bot.*token" public/
   ```

2. **Check .htaccess files:**
   - Review all .htaccess files for malicious redirects

3. **Check cron jobs:**
   ```bash
   crontab -l
   ```

4. **Check for webshells:**
   ```bash
   find public/ -name "*.php" -type f -exec grep -l "shell\|backdoor\|c99\|r57" {} \;
   ```

### 📊 DATA BREACH ASSESSMENT

Based on the submission file found:
- **Stolen Data Types:**
  - Credit card numbers
  - CVV codes
  - Email addresses
  - Passwords
  - SMS verification codes
  - National ID numbers
  - Mobile phone numbers
  - Geolocation data

- **Affected Users:** Unknown (check submission files)

### ⚡ QUICK REMOVAL COMMAND

```bash
# Run this from your project root:
cd /path/to/your/project
rm -rf public/app/IL/
rm -rf public/bots/
rm -rf public/Happy/
find . -name "Exec.ini" -type f -delete
```

### 🛡️ POST-CLEANUP VERIFICATION

After cleanup, verify:
1. No suspicious PHP files remain
2. No unauthorized network connections
3. Server logs show no suspicious activity
4. All legitimate functionality still works

### 📞 IF YOU NEED HELP

1. Contact your hosting provider
2. Consider hiring a security professional
3. Report the breach to relevant authorities if personal data was stolen
4. Notify affected users if applicable

---

**Generated:** $(date)
**Severity:** CRITICAL
**Status:** ACTION REQUIRED IMMEDIATELY


