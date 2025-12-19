# CC Checker Tool - Complete Implementation Guide

## 🎯 Overview

The CC Checker Tool is an advanced, military-grade secured credit card validation system with multi-layered security protection, real-time validation, and comprehensive analytics dashboard.

## 📁 File Structure

```
tools/cc-checker/
├── index.html              # Main checker interface (Enhanced)
├── index-basic.html        # Basic version backup
├── dashboard.html          # Analytics dashboard
├── config.php             # Security configuration
├── api.php                # API request handler
├── auth.php               # Authentication handler
├── security-utils.js      # Advanced security utilities
├── .htaccess             # Apache security rules
└── README.md             # Documentation
```

## 🛡️ Security Architecture

### Layer 1: Client-Side Security (JavaScript)

#### Bot Detection
- **WebDriver Detection**: Identifies automated browsers
- **Plugin Enumeration**: Checks for browser plugins
- **Language Verification**: Validates browser language settings
- **User Agent Analysis**: Detects suspicious user agents
- **Canvas Fingerprinting**: Creates unique browser fingerprints
- **Timing Analysis**: Identifies suspiciously fast execution

#### Input Validation
- **Luhn Algorithm**: Validates card number checksums
- **Expiry Validation**: Checks card expiration dates
- **CVV Validation**: Verifies CVV format (3 or 4 digits)
- **SQL Injection Prevention**: Filters SQL patterns
- **XSS Protection**: Removes HTML tags and scripts

#### Activity Monitoring
- **Mouse Movement Tracking**: Detects bot-like movements
- **Keyboard Activity**: Monitors typing patterns
- **Click Pattern Analysis**: Identifies suspicious clicking
- **Linearity Detection**: Spots perfectly linear mouse paths

#### Rate Limiting
- **Request Throttling**: 10 requests per minute limit
- **Time Window Management**: 60-second sliding window
- **Automatic Reset**: Clears old requests
- **Retry After**: Shows countdown for blocked requests

#### Device Fingerprinting
- **Browser Characteristics**: User agent, language, platform
- **Hardware Profile**: Memory, CPU cores, color depth
- **Screen Resolution**: Display dimensions
- **Timezone Detection**: Geographic fingerprint
- **Canvas Fingerprint**: Unique rendering signature
- **WebGL Fingerprint**: GPU vendor and renderer

### Layer 2: Transport Security

#### Headers
- `Authorization: Bearer <token>` - JWT authentication
- `X-Device-Fingerprint` - Unique device ID
- `X-Client-Version` - Application version
- `Content-Type: application/json` - Data format
- `X-CSRF-Token` - Cross-site request forgery protection

#### Encryption
- HTTPS/TLS transport encryption (in production)
- Token-based session management
- Secure cookie flags (HttpOnly, Secure, SameSite)

### Layer 3: Server-Side Security (PHP)

#### Session Management
- **Strict Session Settings**: HttpOnly, Secure, SameSite
- **Session Timeout**: 30 minutes of inactivity
- **Session Regeneration**: On authentication
- **IP Validation**: Detects session hijacking
- **User Agent Validation**: Prevents session stealing

#### CSRF Protection
- **Token Generation**: Cryptographically secure tokens
- **Token Expiry**: 1-hour lifetime
- **Token Validation**: Hash comparison
- **Per-Request Tokens**: Unique for each form

#### Rate Limiting
- **API Calls**: 10 requests per minute per IP
- **Authentication**: 5 login attempts per 15 minutes
- **Lockout Period**: 15 minutes after limit exceeded
- **Per-User Tracking**: Separate limits per session

#### Input Sanitization
- **HTML Stripping**: Removes all HTML tags
- **SQL Prevention**: Blocks SQL keywords
- **XSS Filtering**: Removes JavaScript
- **Whitespace Trimming**: Cleans input
- **Recursive Sanitization**: Handles arrays

#### Logging
- **Security Events**: All auth attempts logged
- **Card Checks**: Check attempts with BIN
- **Rate Limit Violations**: Blocked requests logged
- **Session Events**: Login, logout, hijack attempts
- **Error Tracking**: All errors captured

### Layer 4: Web Server Security (.htaccess)

#### Access Control
- Directory listing disabled
- Sensitive files blocked
- Backup files hidden
- Configuration files protected

#### Attack Prevention
- SQL injection pattern blocking
- XSS pattern filtering
- Path traversal protection
- Remote file inclusion blocking

#### Headers
- `X-Frame-Options: DENY` - Clickjacking protection
- `X-Content-Type-Options: nosniff` - MIME sniffing protection
- `X-XSS-Protection: 1; mode=block` - XSS filter
- `Referrer-Policy: no-referrer` - Referrer leakage prevention
- `Permissions-Policy` - Feature restrictions

## 🚀 Features

### Card Checking
- **Multi-Gateway Support**: Stripe, Braintree, PayPal, Authorize.net
- **Real-Time Validation**: Instant card verification
- **BIN Lookup**: Card type and issuer identification
- **Response Codes**: Detailed error/success messages
- **Result Display**: Color-coded status indicators

### Dashboard
- **Statistics Cards**: Total checks, approved, declined, success rate
- **Weekly Chart**: 7-day activity visualization
- **Recent Checks**: Last 5 checks with details
- **Status Badges**: Color-coded approval status
- **Quick Actions**: View details buttons

### Security Features
- **Bot Detection**: Automatic bot identification
- **Activity Monitoring**: Real-time behavior analysis
- **Device Fingerprinting**: Unique device identification
- **Rate Limiting**: Client and server-side throttling
- **Secure Storage**: Encrypted session data
- **CSRF Protection**: Token-based form security

## 📊 Dashboard Features

### Statistics
- **Total Checks**: Lifetime check counter
- **Approved Count**: Successful validations
- **Declined Count**: Failed validations
- **Success Rate**: Percentage calculation

### Charts
- **Weekly Activity**: Bar chart showing 7-day trend
- **Interactive**: Hover tooltips with values
- **Responsive**: Adapts to screen size

### Recent Checks Table
- **BIN Masking**: First 6 digits only
- **Gateway Display**: Payment processor used
- **Status Badges**: Color-coded (green/red)
- **Timestamp**: Relative time (e.g., "2 mins ago")
- **Quick Actions**: View details button

## 🔧 Configuration

### Environment Variables
```bash
ENCRYPTION_KEY=your_32_byte_hex_key
STRIPE_API_KEY=your_stripe_key
BRAINTREE_API_KEY=your_braintree_key
PAYPAL_API_KEY=your_paypal_key
```

### PHP Settings (config.php)
```php
define('API_RATE_LIMIT', 10);          // Requests per minute
define('SESSION_TIMEOUT', 1800);        // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);        // Before lockout
define('LOCKOUT_TIME', 900);            // 15 minutes
define('CSRF_TOKEN_EXPIRY', 3600);      // 1 hour
```

### JavaScript Settings
```javascript
const rateLimiter = new ClientRateLimiter(10, 60000); // 10 req/min
const activityMonitor = new ActivityMonitor();
```

## 🎨 User Interface

### Design
- **Dark Theme**: #0a0a0a background
- **Purple Accents**: #7c3aed primary color
- **Gradient Effects**: Multi-color gradients
- **Smooth Animations**: 0.3s transitions
- **Responsive Layout**: Mobile-first design

### Components
- **Sidebar Navigation**: Fixed left sidebar
- **Top Bar**: User profile and title
- **Content Cards**: Glassmorphism effect
- **Form Inputs**: Custom styled inputs
- **Buttons**: Gradient hover effects
- **Result Display**: Color-coded feedback

## 📱 Responsive Design

### Mobile (< 768px)
- Stacked statistics cards
- Hidden sidebar (toggle button)
- Reduced font sizes
- Touch-optimized buttons
- Simplified charts

### Tablet (768px - 1024px)
- 2-column statistics
- Visible sidebar
- Medium font sizes
- Balanced layout

### Desktop (> 1024px)
- 4-column statistics
- Full sidebar
- Large font sizes
- Expanded features

## 🔐 API Endpoints

### Initialize Session
```http
POST /tools/cc-checker/auth.php
Content-Type: application/json

{
  "action": "init_session"
}

Response:
{
  "success": true,
  "csrf_token": "abc123...",
  "session_id": "xyz789..."
}
```

### Verify Token
```http
POST /tools/cc-checker/auth.php
Content-Type: application/json

{
  "action": "verify_token",
  "token": "jwt_token_here"
}

Response:
{
  "success": true,
  "csrf_token": "new_token..."
}
```

### Check Card
```http
POST /tools/cc-checker/api.php
Authorization: Bearer <token>
X-Device-Fingerprint: <fingerprint>
Content-Type: application/json

{
  "action": "check_card",
  "csrf_token": "abc123...",
  "cardNumber": "4532123456789010",
  "expiryMonth": "12",
  "expiryYear": "2025",
  "cvv": "123",
  "gateway": "stripe"
}

Response:
{
  "success": true,
  "result": {
    "status": "approved",
    "message": "Card Live - CVV Matched",
    "code": "00",
    "brand": "Visa",
    "bin": "453212",
    "last4": "9010",
    "gateway": "stripe"
  }
}
```

## 🧪 Testing

### Manual Testing
1. **Bot Detection**: Open in automated browser (should be blocked)
2. **Rate Limiting**: Submit 11 requests in 60 seconds (11th should fail)
3. **Input Validation**: Try invalid card numbers (should show error)
4. **CSRF Protection**: Submit without token (should reject)
5. **Session Timeout**: Wait 30 minutes (should re-authenticate)

### Security Testing
1. **SQL Injection**: Try `1' OR '1'='1` in inputs
2. **XSS**: Try `<script>alert('xss')</script>` in inputs
3. **CSRF**: Submit form from different domain
4. **Session Hijacking**: Change IP during session
5. **Brute Force**: Try multiple failed logins

## 📈 Performance

### Optimizations
- **Lazy Loading**: Security checks on-demand
- **Efficient Fingerprinting**: Cached results
- **Minimal DOM**: Reduced manipulation
- **Event Debouncing**: Activity monitor throttling
- **Request Batching**: Grouped API calls

### Metrics
- **Page Load**: < 2 seconds
- **Bot Detection**: < 500ms
- **Card Validation**: < 1 second
- **Dashboard Render**: < 1 second

## 🚨 Error Handling

### Client-Side Errors
- Invalid card number format
- Expired card
- Invalid CVV
- Rate limit exceeded
- Bot detected
- Session timeout

### Server-Side Errors
- Invalid CSRF token
- Authentication failed
- Database connection error
- API gateway timeout
- Rate limit exceeded

## 📝 Logging

### Log Files
- `logs/security.log` - Security events
- `logs/php_errors.log` - PHP errors
- `logs/api.log` - API requests

### Log Format
```json
{
  "timestamp": "2025-12-19 06:37:54",
  "ip": "192.168.1.1",
  "user_agent": "Mozilla/5.0...",
  "event": "card_check_attempt",
  "details": {
    "gateway": "stripe",
    "bin": "453212"
  }
}
```

## 🔄 Updates & Maintenance

### Regular Tasks
- Review security logs daily
- Update dependencies monthly
- Rotate encryption keys quarterly
- Audit code annually
- Test disaster recovery quarterly

### Version History
- **v2.0.0** - Advanced security features, dashboard
- **v1.0.0** - Initial release with PHP protection

## 📞 Support

For issues or questions:
- **Telegram**: [@legend_bl](https://t.me/legend_bl)
- **Email**: LEGENDXKEYGRID@GMAIL.COM

## 📄 License

ISC License - See main repository LICENSE file

---

**⚠️ Security Notice**: This tool is designed for authorized testing only. Unauthorized use of payment card information is illegal and punishable by law.

**Built with ❤️ by LEGEND SHOP**
