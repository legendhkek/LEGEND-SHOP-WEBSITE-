# Legend Shop Tools

This folder contains various tools and utilities for Legend Shop users.

## Available Tools

### LEGEND CHECKER (Recommended)
**File:** `legend-checker.html`

Advanced CC checker with multiple gateway support, including:
- Dashboard with real-time statistics
- Vault system for saving checked cards
- Leaderboard to track top users
- Integrated credit system
- Redeem code functionality

**Features:**
- Multiple gateway checkers (Stripe, Braintree, PayPal, etc.)
- Real-time card validation
- BIN lookup integration
- Card details storage
- Credit-based usage system

### Admin Panel
**File:** `admin.html`

Admin interface for managing the LEGEND CHECKER system:
- Generate redeem codes with custom credit amounts
- Set expiration dates for codes
- View all generated codes and their status
- Track code usage statistics
- Delete unused codes

**Access:** Admin users only

## Credit System

Users earn and spend credits to use the LEGEND CHECKER tool:
- New users start with 100 credits
- Credits can be added via redeem codes
- Admins can generate redeem codes
- Each check operation costs credits

## API Endpoints

### User Endpoints
- `GET /api/user-credits` - Get current user's credit balance
- `POST /api/redeem-code` - Redeem a code for credits
- `GET /api/credit-transactions` - View credit transaction history

### Admin Endpoints
- `POST /api/admin/generate-code` - Generate new redeem code
- `GET /api/admin/codes` - List all redeem codes
- `DELETE /api/admin/codes/:id` - Delete a redeem code
- `POST /api/admin/add-credits` - Add credits to a user

## Usage

1. Navigate to `/tools/` to see all available tools
2. Click on "LEGEND CHECKER" to access the CC checker
3. Use credits to perform checks
4. Redeem codes to get more credits
5. Admins can access the admin panel to manage codes

## Installation

All tools are integrated with the main Legend Shop application. No additional installation required.

## Security

- All endpoints are protected with JWT authentication
- Admin endpoints require admin privileges
- Redeem codes are single-use only
- All transactions are logged

## Support

For issues or feature requests, contact:
- Telegram: [@legend_bl](https://t.me/legend_bl)
- Email: LEGENDXKEYGRID@GMAIL.COM
