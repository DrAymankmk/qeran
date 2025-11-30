# 📋 QERAN - Modern Invitation System
## Complete Project Description & Enhancement Recommendations

---

## 🎯 PROJECT OVERVIEW

**Qeran** (Modern Invitation System) is a comprehensive digital invitation management platform built with Laravel 9. The system enables users to create, manage, and distribute digital invitations for events, weddings, and parties through multiple channels including mobile apps, SMS, WhatsApp, and web links.

### Core Purpose
The platform serves as a complete solution for:
- Creating personalized digital invitations
- Managing guest lists and RSVPs
- Tracking invitation delivery and engagement
- Processing payments for invitation packages
- Multi-channel communication (SMS, WhatsApp, Email, Push Notifications)
- Admin oversight and approval workflows

---

## 🏗️ TECHNOLOGY STACK

### Backend
- **Framework:** Laravel 9.19
- **PHP Version:** 8.0.2+
- **Database:** MySQL
- **Authentication:** Laravel Sanctum (API tokens)
- **File Storage:** Laravel Storage (local/S3 compatible)

### Frontend
- **Admin Panel:** Bootstrap-based responsive interface
- **Mobile API:** RESTful API (v1)
- **Build Tool:** Vite 4.0
- **JavaScript:** Axios, Lodash

### Key Dependencies
- `astrotomic/laravel-translatable` - Multi-language support (Arabic/English)
- `spatie/laravel-permission` - Role-based access control
- `spatie/laravel-activitylog` - Activity logging
- `simplesoftwareio/simple-qrcode` - QR code generation
- `intervention/image` - Image processing
- `pbmedia/laravel-ffmpeg` - Video/audio processing
- `mpdf/mpdf` - PDF generation
- `twilio/sdk` - SMS messaging
- `pusher/pusher-php-server` - Push notifications

---

## 📱 CORE FUNCTIONALITY

### 1. USER MANAGEMENT SYSTEM

#### User Registration & Authentication
- **Phone/Email Registration:** Users can register using phone number or email
- **Verification System:** 
  - SMS verification codes via Twilio
  - Email verification
  - WhatsApp verification
- **Multi-language Support:** Arabic and English
- **Account Types:** Regular users, Admins, Guards
- **Profile Management:** Update name, email, phone, profile image, language preference

#### User Roles & Permissions
- **Regular Users:** Create invitations, invite guests
- **Invitation Admins:** Co-manage specific invitations
- **Guards:** Security personnel for events
- **Extra Guards:** Additional security staff
- **System Admins:** Full system access via admin panel

#### User Status Management
- Verified / Not Verified / Suspended
- Soft delete support
- Account deletion with instructions

---

### 2. INVITATION MANAGEMENT SYSTEM

#### Invitation Types
1. **App Design (Type 1):** Pre-designed templates from the application
2. **Contact Design (Type 2):** Templates from category/contact designs
3. **User Design (Type 3):** User-uploaded custom designs (images, videos, audio)

#### Invitation Creation Workflow (7 Steps)
1. **Upload Invitation:** Upload design files (image/video/audio)
2. **Choose Package:** Select invitation package (static or dynamic pricing)
3. **Invite Users:** Add guests to the invitation
4. **Add Guard:** Assign security guards for the event
5. **Add Admin:** Assign co-administrators
6. **Add Payment:** Upload payment receipt for package
7. **Update Invitation:** Edit invitation details

#### Invitation Features
- **Unique Codes:** Auto-generated 8-character unique invitation codes
- **QR Code Generation:** Individual QR codes per invitation-user pair
- **Geolocation:** Latitude/longitude for event location
- **Event Details:** Date, time, address, description
- **Wedding Fields:** Groom, bride, groom father, bride father names
- **Event Name:** Custom event naming
- **Host Name:** Event host information
- **Media Support:** Images, videos, audio files
- **Slug Generation:** SEO-friendly URLs

#### Invitation Status Tracking
- **Approved (1):** Admin approved, active
- **Pending Admin (2):** Waiting for admin approval
- **Pending User Approval (3):** Waiting for user confirmation
- **Rejected (4):** Admin rejected
- **Cancelled (-1):** User cancelled
- **Finished Invitation (5):** Event completed (auto-set 8 hours after event time)

#### Invitation Status for Guests (SEEN_STATUS)
- `0` - Not in the app
- `1` - In app
- `2` - Seen
- `3` - Delivered
- `4` - Scanned (QR code scanned)
- `5` - All not attended
- `6` - Sent
- `7` - Accepted
- `8` - Declined
- `9` - Did not attend

#### Invitation Sharing Methods
- **Web Link:** Unique invitation URLs
- **QR Code:** Generated QR codes for scanning
- **SMS:** Send invitation via SMS
- **WhatsApp:** Share via WhatsApp
- **Email:** Email invitations

---

### 3. PACKAGE SYSTEM

#### Package Types
1. **Static Package:** Fixed price regardless of invitation count
2. **Dynamic Package:** Price per invitation (variable pricing)

#### Package Features
- **Free Invitations Count:** Base number of free invitations
- **Additional Invitations:** Can purchase extra invitations
- **Package Status:** Active/Inactive
- **Invitation Type Association:** Packages linked to invitation types
- **Price Management:** Admin-controlled pricing

#### Payment System
- **Payment Status:**
  - Paid (1)
  - Not Paid (2)
  - Pending Admin Payment (3)
- **Receipt Upload:** Users upload payment receipts
- **Admin Verification:** Admins verify and approve payments
- **Email Notifications:** Automatic email to admin on receipt upload
- **Multiple Packages:** Invitations can have multiple packages (base + extras)

#### Invitation Counting
- **Base Package Count:** From selected package
- **Extra Package Count:** Additional purchased invitations
- **Total Available:** Sum of all paid packages
- **Used Count:** Track invitations sent
- **Remaining Count:** Available invitations remaining

---

### 4. CATEGORY SYSTEM

#### Category Types
- **Event (1):** General events
- **Wedding (2):** Wedding-specific
- **Party (3):** Party events

#### Category Features
- **Multi-language:** Arabic and English translations
- **Category Fields:** Name, slug, description, title
- **Status Management:** Active/Inactive
- **Image Support:** Category images
- **Invitation Association:** Invitations linked to categories

---

### 5. NOTIFICATION SYSTEM

#### Notification Channels
1. **Push Notifications:** Via Pusher Beams
   - Real-time notifications to mobile apps
   - Token-based authentication
2. **SMS Notifications:** Via Twilio
   - Verification codes
   - Invitation links
   - Status updates
3. **WhatsApp Integration:** Via UltraMessage
   - WhatsApp Business API
   - Webhook support for message acknowledgments
   - Template messages
4. **Email Notifications:** Laravel Mail
   - Invitation notifications
   - Payment receipts
   - Admin alerts

#### Notification Types
- **Admin (0):** System/admin notifications
- **Invitations (1):** New invitation notifications
- **Updated Invitations (2):** Invitation update notifications
- **Invitation Request (3):** Request-related notifications

#### Notification Features
- **Multi-language:** Arabic and English content
- **Read Status:** Track read/unread notifications
- **User-specific:** Targeted notifications
- **Bulk Notifications:** Send to multiple users
- **Notification Count:** Track unread count per user

---

### 6. GUEST MANAGEMENT

#### Guest Roles
- **User (1):** Regular guest
- **Admin (2):** Co-administrator of invitation
- **Guard (3):** Security guard
- **Extra Guard (4):** Additional security

#### Guest Features
- **Invitation Count:** Track how many invitations sent per guest
- **Host Name:** Custom name for guest
- **Status Tracking:** See, delivered, accepted, declined tracking
- **Password Protection:** Optional password for guards
- **Bulk Invite:** Add multiple guests at once
- **Edit Guest:** Update guest information
- **Remove Guest:** Remove from invitation

---

### 7. ADMIN PANEL

#### Dashboard
- Overview statistics
- Recent activities
- Quick actions

#### User Management
- View all users
- Edit user details
- Suspend/activate users
- Export user data (PDF)
- User status management

#### Invitation Management
- View all invitations
- Filter by status, date, type
- Approve/reject invitations
- View invitation details
- Manage invitation packages
- View guards and admins
- Export invitations (PDF)
- Change invitation status
- Package status management

#### Package Management
- Create/edit packages
- Set pricing
- Manage package types
- Activate/deactivate packages
- Export packages (PDF)

#### Category Management
- Create/edit categories
- Multi-language support
- Upload category images
- Manage category types
- Export categories (PDF)

#### Notification Management
- Send notifications
- View notification history
- Multi-language notifications
- Export notifications (PDF)

#### Contact Management
- View contact submissions
- Reply to contacts via email
- Filter by type (Contact, Newsletter, Suggestion)
- Export contacts (PDF)

#### App Settings
- Configure application settings
- Terms & Conditions
- About Us
- Contact Information
- Multi-language content

#### Financial Management
- View financial reports
- Monthly reports
- Annual reports
- Revenue tracking
- Chart data visualization
- Export financial reports (PDF)

#### Promo Code Management
- Create promo codes
- Set discount percentages
- Package-specific or general codes
- Usage limits
- Expiration dates
- Track usage count
- Activate/deactivate codes

---

### 8. API ENDPOINTS

#### Authentication Endpoints
- `POST /api/v1/login` - User login
- `POST /api/v1/register` - User registration
- `POST /api/v1/verify` - Verify code
- `POST /api/v1/send_code` - Send verification code
- `POST /api/v1/change_password` - Change password
- `POST /api/v1/auth/logout` - Logout
- `GET /api/v1/auth/delete` - Delete account
- `POST /api/v1/login-guard` - Guard login
- `GET /api/v1/generate-beams-token` - Generate push notification token
- `POST /api/v1/auth/change-language` - Change user language

#### Invitation Endpoints
- `GET /api/v1/invitations` - List invitations (with filters)
- `POST /api/v1/invitations/store` - Create invitation
- `GET /api/v1/invitations/{id}` - Get invitation details
- `POST /api/v1/invitations/update/{id}` - Update invitation
- `POST /api/v1/invitations/add-user/{id}` - Add guest
- `POST /api/v1/invitations/add-admin/{id}` - Add admin
- `POST /api/v1/invitations/add-guard/{id}` - Add guard
- `POST /api/v1/invitations/edit-user/{user}` - Edit guest
- `POST /api/v1/invitations/user/delete/{invitation}` - Remove guest
- `POST /api/v1/invitations/status/{id}` - Update invitation status
- `GET /api/v1/invitations/share/{id}` - Get share link
- `GET /api/v1/invitations/share-sms/{id}` - Get SMS share link
- `GET /api/v1/invitations/packages/{id}` - Get invitation packages
- `POST /api/v1/invitations/payment/receipt/{id}` - Upload payment receipt
- `POST /api/v1/invitations/add-extra-package/{id}` - Add extra package
- `GET /api/v1/invitations/invited/users/{id}` - Get invited users
- `GET /api/v1/invitations/invited/admins/{id}` - Get invitation admins
- `GET /api/v1/invitations/invited/guards/{id}` - Get guards
- `POST /api/v1/invitations/send-notification/{id}` - Send notification
- `POST /api/v1/invitations/send-sms/{id}` - Send SMS
- `POST /api/v1/invitations/send-template-message/{id}` - Send template message
- `GET /api/v1/invitations/check/invitation` - Check invitation
- `GET /api/v1/invitations/complete-request-invitation/{id}` - Complete request invitation

#### Profile Endpoints
- `GET /api/v1/profile` - Get user profile
- `POST /api/v1/profile/update` - Update profile

#### Notification Endpoints
- `GET /api/v1/notifications` - List notifications
- `GET /api/v1/notifications/delete/{id}` - Delete notification

#### Home & Settings
- `GET /api/v1/home` - Get home data (categories, packages)
- `GET /api/v1/settings` - Get app settings
- `POST /api/v1/contact-us` - Submit contact form
- `GET /api/v1/app-settings` - Get application settings

#### Webhooks
- `POST /api/v1/whatsapp-webhook` - WhatsApp webhook handler

---

### 9. WEBSITE FEATURES

#### Public Invitation View
- View invitation by code and user ID
- Accept/decline invitation
- Responsive design
- Multi-language support

#### Privacy Policy
- Privacy policy page
- Terms and conditions

#### Account Deletion Instructions
- Instructions for account deletion

---

### 10. FILE MANAGEMENT

#### File Types Supported
- **Images:** JPEG, PNG, GIF
- **Videos:** MP4, AVI, etc.
- **Audio:** MP3, WAV, etc.

#### File Storage Structure
```
storage/app/public/
├── users/              # User profile images
├── admins/             # Admin profile images
├── categories/         # Category images
└── invitations/
    ├── images/        # Invitation images
    ├── main_images/   # Main invitation images
    ├── video/         # Invitation videos
    ├── audio/         # Invitation audio
    └── receipts/      # Payment receipts
```

#### File Processing
- Image resizing via Intervention Image
- Video/audio processing via FFmpeg
- Thumbnail generation
- Multiple file associations (morphable)

---

### 11. REPORTING & EXPORTS

#### PDF Export Features
- User export
- Invitation export
- Package export
- Category export
- Notification export
- Contact export
- Financial reports export
- Promo code export

#### Financial Reports
- Monthly reports
- Annual reports
- Revenue charts
- Payment tracking

---

## 🚀 ENHANCEMENT & FEATURE RECOMMENDATIONS

### 🔴 HIGH PRIORITY ENHANCEMENTS

#### 1. Security Enhancements
- **Remove All Malicious Code:** Delete phishing kits and suspicious files
- **Implement Rate Limiting:** Add rate limiting to API endpoints
- **Add CSRF Protection:** Ensure all forms have CSRF tokens
- **Input Sanitization:** Enhance input validation and sanitization
- **SQL Injection Prevention:** Review all raw queries
- **XSS Protection:** Implement proper output escaping
- **File Upload Security:** Add virus scanning, file type validation
- **API Rate Limiting:** Implement per-user rate limits
- **Two-Factor Authentication:** Add 2FA for admin accounts
- **Security Headers:** Implement security headers (CSP, HSTS, etc.)

#### 2. Payment Gateway Integration
- **Online Payment Gateways:**
  - Stripe integration
  - PayPal integration
  - Local payment gateways (based on region)
- **Automatic Payment Verification:** Auto-verify payments via webhooks
- **Payment History:** User payment history
- **Refund System:** Handle refunds
- **Invoice Generation:** Automatic invoice generation
- **Payment Plans:** Subscription-based packages

#### 3. Real-time Features
- **WebSocket Integration:** Real-time updates using Laravel Echo
- **Live Invitation Status:** Real-time status updates
- **Live Chat:** Support chat system
- **Real-time Notifications:** Instant push notifications
- **Live Event Tracking:** Real-time event attendance tracking

#### 4. Analytics & Reporting
- **User Analytics Dashboard:** User behavior analytics
- **Invitation Analytics:**
  - Open rates
  - Click-through rates
  - Acceptance rates
  - Geographic distribution
- **Revenue Analytics:** Detailed revenue reports
- **Export Options:** Excel, CSV exports
- **Custom Reports:** Admin-configurable reports

#### 5. Mobile App Enhancements
- **Offline Mode:** Cache invitations for offline viewing
- **Push Notification Improvements:** Rich notifications
- **Deep Linking:** Better deep link handling
- **App Updates:** In-app update notifications
- **Biometric Authentication:** Fingerprint/Face ID login

---

### 🟡 MEDIUM PRIORITY ENHANCEMENTS

#### 6. Advanced Invitation Features
- **Invitation Templates Library:** Expandable template library
- **Custom Design Editor:** In-app design editor
- **Video Invitations:** Enhanced video support
- **Interactive Invitations:** Interactive elements
- **RSVP Forms:** Custom RSVP forms
- **Gift Registry:** Gift registry integration
- **Event Calendar Integration:** Google Calendar, iCal export
- **Weather Integration:** Weather forecast for event date
- **Map Integration:** Enhanced map features (Google Maps, Apple Maps)

#### 7. Social Features
- **Social Media Sharing:** Enhanced social sharing
- **Invitation Comments:** Guest comments on invitations
- **Photo Gallery:** Event photo gallery
- **Guest Book:** Digital guest book
- **Event Feed:** Activity feed for events
- **Social Login:** Login with Google, Facebook, Apple

#### 8. Communication Enhancements
- **Email Templates:** Rich email templates
- **SMS Templates:** Customizable SMS templates
- **WhatsApp Templates:** Approved WhatsApp templates
- **Bulk Messaging:** Send bulk messages to guests
- **Message Scheduling:** Schedule messages
- **Auto-reminders:** Automatic reminder system
- **Multi-language Messages:** Auto-translate messages

#### 9. Admin Panel Improvements
- **Advanced Filters:** More filtering options
- **Bulk Actions:** Bulk approve/reject
- **Activity Log:** Detailed activity logging
- **User Impersonation:** Admin can login as user
- **Advanced Search:** Full-text search
- **Dashboard Widgets:** Customizable dashboard
- **Export Scheduling:** Scheduled exports
- **Backup System:** Automated backups

#### 10. Performance Optimizations
- **Caching Strategy:** Redis/Memcached implementation
- **Database Optimization:** Query optimization, indexing
- **CDN Integration:** Content delivery network
- **Image Optimization:** Automatic image compression
- **Lazy Loading:** Implement lazy loading
- **API Response Caching:** Cache API responses
- **Database Query Optimization:** Eager loading, query optimization

---

### 🟢 LOW PRIORITY ENHANCEMENTS

#### 11. Advanced Features
- **AI-Powered Suggestions:** AI suggestions for invitations
- **Voice Invitations:** Voice message invitations
- **AR/VR Support:** Augmented reality invitations
- **Blockchain Verification:** Blockchain-based verification
- **NFT Invitations:** Non-fungible token invitations
- **Smart Contracts:** Automated smart contracts

#### 12. Integration Enhancements
- **CRM Integration:** Salesforce, HubSpot integration
- **Calendar Integration:** Full calendar sync
- **Email Marketing:** Mailchimp, SendGrid integration
- **Analytics Integration:** Google Analytics, Mixpanel
- **Social Media Integration:** Instagram, Facebook integration
- **Event Management Tools:** Integration with event platforms

#### 13. User Experience Improvements
- **Dark Mode:** Dark theme support
- **Accessibility:** WCAG compliance
- **Multi-device Sync:** Sync across devices
- **Voice Commands:** Voice-activated features
- **Gesture Support:** Swipe gestures
- **Haptic Feedback:** Tactile feedback

#### 14. Business Features
- **White-label Solution:** White-label for resellers
- **Affiliate Program:** Affiliate marketing system
- **Referral Program:** User referral system
- **Loyalty Program:** Points and rewards
- **Subscription Plans:** Monthly/yearly subscriptions
- **Enterprise Features:** Enterprise-level features

#### 15. Content Management
- **CMS Integration:** Content management system
- **Blog System:** Blog for announcements
- **FAQ System:** Frequently asked questions
- **Knowledge Base:** User documentation
- **Video Tutorials:** In-app tutorials
- **Help Center:** Comprehensive help center

---

## 📊 DATABASE SCHEMA OVERVIEW

### Core Tables
- **users:** User accounts
- **admins:** Admin accounts
- **invitations:** Invitation records
- **invitation_user:** Pivot table (invitations ↔ users)
- **packages:** Invitation packages
- **invitation_package:** Pivot table (invitations ↔ packages)
- **categories:** Invitation categories
- **category_translations:** Category translations
- **notifications:** User notifications
- **notification_translations:** Notification translations
- **hub_files:** File storage (polymorphic)
- **verification_codes:** Verification codes
- **contact_us:** Contact submissions
- **app_settings:** Application settings
- **settings:** Settings with translations
- **setting_translations:** Setting translations
- **promo_codes:** Promotional codes

---

## 🔧 TECHNICAL IMPROVEMENTS

### Code Quality
- **Unit Tests:** Comprehensive test coverage
- **Integration Tests:** API integration tests
- **Code Standards:** PSR-12 compliance
- **Documentation:** PHPDoc comments
- **Code Review:** Implement code review process
- **CI/CD Pipeline:** Automated testing and deployment

### Architecture
- **Repository Pattern:** Implement repository pattern
- **Service Layer:** Enhance service layer
- **Event-Driven Architecture:** Use Laravel events
- **Queue System:** Implement queue for heavy tasks
- **Job Scheduling:** Automated job scheduling
- **Microservices:** Consider microservices for scalability

### Monitoring & Logging
- **Error Tracking:** Sentry or similar
- **Performance Monitoring:** APM tools
- **Log Aggregation:** Centralized logging
- **Uptime Monitoring:** Server monitoring
- **Alerting System:** Automated alerts

---

## 📈 BUSINESS ENHANCEMENTS

### Marketing Features
- **Email Campaigns:** Marketing email campaigns
- **SMS Campaigns:** Marketing SMS campaigns
- **Push Campaigns:** Marketing push notifications
- **A/B Testing:** Test different invitation designs
- **Conversion Tracking:** Track conversion rates

### Revenue Optimization
- **Dynamic Pricing:** AI-based pricing
- **Upselling:** Package upselling
- **Cross-selling:** Related package suggestions
- **Discount System:** Enhanced discount system
- **Loyalty Rewards:** Reward loyal users

---

## 🎯 IMPLEMENTATION PRIORITY MATRIX

### Phase 1 (Immediate - 1-2 months)
1. Security enhancements
2. Payment gateway integration
3. Basic analytics
4. Performance optimizations

### Phase 2 (Short-term - 3-4 months)
5. Real-time features
6. Advanced invitation features
7. Admin panel improvements
8. Communication enhancements

### Phase 3 (Medium-term - 5-6 months)
9. Social features
10. Advanced analytics
11. Integration enhancements
12. UX improvements

### Phase 4 (Long-term - 7+ months)
13. AI features
14. Advanced integrations
15. Enterprise features
16. White-label solution

---

## 📝 CONCLUSION

The Qeran Modern Invitation System is a comprehensive platform with robust core functionality. The recommended enhancements focus on:

1. **Security:** Critical security improvements
2. **User Experience:** Better UX and features
3. **Business Growth:** Revenue and marketing features
4. **Scalability:** Performance and architecture improvements
5. **Innovation:** Advanced features for competitive advantage

The system has a solid foundation and with these enhancements, it can become a market-leading invitation management platform.

---

**Document Version:** 1.0  
**Last Updated:** 2024  
**Project:** Qeran - Modern Invitation System  
**Framework:** Laravel 9.19





