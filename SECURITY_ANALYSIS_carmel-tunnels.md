# Security Analysis: carmel-tunnels.modern-invitation.com

## 🔍 File Analysis

### 1. `r.php` - ⚠️ SUSPICIOUS

**Content:** `asbaasbaba`

**Status:** **SUSPICIOUS - RECOMMEND DELETION**

**Analysis:**
- Contains only meaningless text: "asbaasbaba"
- No PHP code, no functionality
- Could be:
  - A marker file left by attackers
  - A test file
  - A corrupted file
  - Part of an attack chain

**Recommendation:** **DELETE THIS FILE** - It serves no legitimate purpose.

---

### 2. `api.php` - ⚠️ SECURITY VULNERABILITIES

**Purpose:** Form submission handler and API proxy for Sitejet/Sitehub website builder

**Security Issues Found:**

#### 🔴 Critical Issues:

1. **Path Traversal Vulnerability**
   ```php
   $fileContent = $attachment['data'] ?? file_get_contents($attachment['tempFile']);
   ```
   - Uses `file_get_contents()` on user-controlled file paths
   - No validation of file paths
   - Could allow reading arbitrary files on server

2. **Email Injection Vulnerability**
   ```php
   return mail($destinationEmail, $subject, $mailBody, $headers, $senderEmail ? "-f $senderEmail" : "");
   ```
   - Direct use of PHP `mail()` function
   - No sanitization of email headers
   - Vulnerable to email header injection attacks

3. **No Input Validation**
   - Form data processed without validation
   - No sanitization of user input
   - No type checking

4. **No CSRF Protection**
   - Form submissions lack CSRF tokens
   - Vulnerable to cross-site request forgery

5. **No Rate Limiting**
   - No protection against spam/abuse
   - Can be used for email spam attacks

6. **Unsafe File Upload Handling**
   ```php
   'body' => filesize($fileData['tempFile']) < 1024 * 1024 * 1024 ? base64_encode(file_get_contents($fileData['tempFile'])) : 'too_large',
   ```
   - No file type validation
   - No virus scanning
   - Large files (up to 1GB) can be processed

7. **Unsafe cURL Usage**
   - No SSL verification in some cases
   - Forwards requests to external APIs without validation

#### 🟡 Medium Issues:

1. **Information Disclosure**
   - Error messages may leak system information
   - API endpoints exposed

2. **Weak Security Headers**
   - No security headers set
   - No content security policy

---

## 🛡️ Security Recommendations

### Immediate Actions:

1. **Delete `r.php`**
   ```bash
   rm carmel-tunnels.modern-invitation.com/r.php
   ```

2. **Secure `api.php`** - Apply the following fixes:

#### Fix 1: Add Input Validation
```php
function validateFormData($data) {
    // Validate and sanitize all input
    $sanitized = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $sanitized[$key] = array_map('htmlspecialchars', $value);
        } else {
            $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    return $sanitized;
}
```

#### Fix 2: Secure File Path Handling
```php
// Validate file paths to prevent directory traversal
function validateFilePath($filePath) {
    $realPath = realpath($filePath);
    $allowedDir = realpath(__DIR__ . '/uploads/');
    
    if ($realPath === false || strpos($realPath, $allowedDir) !== 0) {
        throw new Exception('Invalid file path');
    }
    
    return $realPath;
}
```

#### Fix 3: Sanitize Email Headers
```php
function sanitizeEmail($email) {
    // Remove newlines and carriage returns to prevent header injection
    $email = str_replace(["\r", "\n"], '', $email);
    return filter_var($email, FILTER_SANITIZE_EMAIL);
}
```

#### Fix 4: Add CSRF Protection
```php
session_start();

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
```

#### Fix 5: Add Rate Limiting
```php
function checkRateLimit($ip, $maxRequests = 10, $timeWindow = 3600) {
    $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($ip) . '.txt';
    $requests = [];
    
    if (file_exists($cacheFile)) {
        $requests = json_decode(file_get_contents($cacheFile), true) ?: [];
    }
    
    // Remove old requests
    $requests = array_filter($requests, function($time) use ($timeWindow) {
        return (time() - $time) < $timeWindow;
    });
    
    if (count($requests) >= $maxRequests) {
        http_response_code(429);
        die('Rate limit exceeded. Please try again later.');
    }
    
    $requests[] = time();
    file_put_contents($cacheFile, json_encode($requests));
}
```

#### Fix 6: Validate File Types
```php
function validateFileType($file, $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file);
    finfo_close($finfo);
    
    return in_array($mimeType, $allowedTypes);
}
```

---

## 📋 Complete Security Checklist

### For `api.php`:

- [ ] Delete `r.php` file
- [ ] Add input validation and sanitization
- [ ] Implement CSRF protection
- [ ] Add rate limiting
- [ ] Secure file upload handling
- [ ] Validate file paths (prevent directory traversal)
- [ ] Sanitize email headers (prevent email injection)
- [ ] Add file type validation
- [ ] Implement file size limits
- [ ] Add logging for security events
- [ ] Set security headers
- [ ] Add error handling (don't expose system info)
- [ ] Review and secure cURL usage
- [ ] Consider using Laravel's form handling instead

### Alternative Solution:

**Consider migrating form handling to Laravel:**
- Use Laravel's built-in CSRF protection
- Use Laravel's validation system
- Use Laravel's Mail facade (more secure than PHP mail())
- Use Laravel's file storage system
- Better security by default

---

## 🚨 Current Risk Level

**`r.php`:** 🔴 **HIGH RISK** - Delete immediately  
**`api.php`:** 🟡 **MEDIUM-HIGH RISK** - Multiple vulnerabilities, needs immediate fixes

---

## 📝 Notes

- The `carmel-tunnels.modern-invitation.com` directory appears to be legitimate website code
- However, it's being impersonated by the phishing kit in `public/app/IL/`
- The phishing kit uses "Carmel Tunnels" branding to steal credentials
- The website code itself is not malicious, but has security vulnerabilities

---

**Generated:** $(date)  
**Priority:** MEDIUM-HIGH - Fix vulnerabilities before they're exploited


