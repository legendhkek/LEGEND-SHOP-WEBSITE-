# CC Checker Tool - Security Documentation

## Overview

The CC Checker tool is a secure credit card validation system with advanced PHP-based security protection and multi-gateway support.

## Security Features

### 1. **PHP Security Layer**
- **CSRF Protection**: All requests require valid CSRF tokens
- **Session Management**: Secure session handling with strict cookie settings
- **Rate Limiting**: Prevents abuse with configurable request limits (10 requests/minute)
- **Input Sanitization**: All inputs are sanitized to prevent XSS and injection attacks
- **JWT Token Validation**: Integration with Node.js backend for authentication

### 2. **Apache Security (.htaccess)**
- Directory listing disabled
- Protection against clickjacking
- SQL injection prevention rules
- XSS attack mitigation
- Hidden file protection
- Request method limitations

### 3. **Advanced Protection**
- **Encryption**: Sensitive data encrypted with AES-256-CBC
- **Security Headers**: 
  - X-Frame-Options: DENY
  - X-Content-Type-Options: nosniff
  - X-XSS-Protection: 1; mode=block
  - Referrer-Policy: no-referrer
  - Content-Security-Policy configured
- **Session Security**: 
  - Strict session timeout (30 minutes)
  - Session hijacking detection
  - IP address validation
- **Logging**: All security events are logged for monitoring

### 4. **API Security**
- Token-based authentication (JWT)
- Rate limiting per IP address
- Request validation
- CORS protection
- Method restrictions (POST only for sensitive operations)

## File Structure

```
cc-checker/
├── index.html          # Frontend interface
├── config.php          # Security configuration
├── api.php            # API request handler
├── auth.php           # Authentication handler
├── .htaccess          # Apache security rules
└── README.md          # This file
```

## Features

### Card Checking
- Multi-gateway support (Stripe, Braintree, PayPal, Authorize.net)
- Real-time card validation
- BIN lookup integration
- Luhn algorithm validation
- Response codes and messages

### User Interface
- Modern dark theme matching LEGEND SHOP design
- Responsive layout (mobile & desktop)
- Real-time validation feedback
- Loading states
- Error handling

## Usage

### Prerequisites
- PHP 7.4 or higher
- Apache web server with mod_rewrite enabled
- Node.js backend running (for JWT validation)
- Valid authentication token

### Access
1. Navigate to `/tools/cc-checker/index.html`
2. User must be authenticated (redirects to login if not)
3. PHP session is automatically initialized with CSRF protection
4. Submit card details through the secure form

### API Endpoints

#### Initialize Session
```
POST /tools/cc-checker/auth.php
Body: { "action": "init_session" }
Response: { "success": true, "csrf_token": "...", "session_id": "..." }
```

#### Verify Token
```
POST /tools/cc-checker/auth.php
Body: { "action": "verify_token", "token": "..." }
Response: { "success": true, "csrf_token": "..." }
```

#### Check Card
```
POST /tools/cc-checker/api.php
Headers: { "Authorization": "Bearer <token>" }
Body: {
  "action": "check_card",
  "csrf_token": "...",
  "cardNumber": "4532123456789010",
  "expiryMonth": "12",
  "expiryYear": "2025",
  "cvv": "123",
  "gateway": "stripe"
}
Response: {
  "success": true,
  "result": {
    "status": "approved|declined|invalid",
    "message": "...",
    "code": "00",
    "brand": "Visa",
    "bin": "453212",
    "last4": "9010",
    "gateway": "stripe"
  }
}
```

#### Get BIN Info
```
POST /tools/cc-checker/api.php
Headers: { "Authorization": "Bearer <token>" }
Body: {
  "action": "get_bin_info",
  "csrf_token": "...",
  "bin": "453212"
}
```

## Security Best Practices

### For Administrators
1. **Environment Variables**: Store sensitive API keys in environment variables
2. **HTTPS**: Always use HTTPS in production
3. **Monitoring**: Regularly check security logs at `/logs/security.log`
4. **Updates**: Keep PHP and Apache updated
5. **Permissions**: Set proper file permissions (644 for files, 755 for directories)

### For Developers
1. Always validate and sanitize user input
2. Never expose sensitive data in responses
3. Use prepared statements for database queries
4. Implement proper error handling
5. Regular security audits

## Rate Limiting

- **API Requests**: 10 requests per minute per IP
- **Authentication**: 5 login attempts before 15-minute lockout
- **Session Timeout**: 30 minutes of inactivity
- **CSRF Token**: 1 hour expiry

## Error Handling

All errors are logged to `/logs/security.log` with details:
- Timestamp
- IP address
- User agent
- Event type
- Error details

## Logging

Security events logged include:
- Authentication attempts (success/failure)
- Card check attempts
- Rate limit violations
- CSRF token validation failures
- Session hijacking attempts
- Invalid token usage

## Integration with Node.js Backend

The PHP layer integrates seamlessly with the Node.js Express backend:
- JWT token validation
- User credit management
- Vault operations
- Transaction history

## API Gateway Support

Currently supports checking via:
- **Stripe**: Full card validation
- **Braintree**: Advanced fraud detection
- **PayPal**: Account verification
- **Authorize.net**: Real-time authorization

*Note: Gateway API calls are simulated in demo mode. Production deployment requires actual API credentials.*

## Compliance

- **PCI DSS**: Does not store full card numbers
- **GDPR**: Logs contain no personally identifiable information
- **Security**: Follows OWASP Top 10 recommendations

## Troubleshooting

### Common Issues

1. **CSRF Token Error**
   - Clear cookies and refresh page
   - Check session timeout settings

2. **Rate Limit Exceeded**
   - Wait 1 minute before retrying
   - Check for multiple tabs/windows

3. **Authentication Failed**
   - Verify token is valid
   - Check Node.js backend is running
   - Confirm token not expired

4. **Apache Errors**
   - Check mod_rewrite is enabled
   - Verify .htaccess syntax
   - Check file permissions

## Future Enhancements

- [ ] Real payment gateway integration
- [ ] Enhanced BIN database
- [ ] Multi-factor authentication
- [ ] Advanced fraud detection
- [ ] Batch card checking
- [ ] Export results to CSV
- [ ] Detailed analytics dashboard

## Support

For issues or questions:
- Telegram: [@legend_bl](https://t.me/legend_bl)
- Email: LEGENDXKEYGRID@GMAIL.COM

## License

ISC License - See main repository LICENSE file

---

**Security Notice**: This tool is designed for authorized testing only. Unauthorized use of payment card information is illegal.
