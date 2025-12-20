# Google OAuth Setup Guide

This guide explains how to properly configure Google OAuth for the Legend Shop website to fix the 404 callback error.

## Problem Description

When users sign up or log in with Google, they were encountering a 404 error at the callback URL:
```
https://legendbl.tech/auth/google/callback?code=...
```

## Root Causes

1. **Incorrect redirect URI construction**: The server was using `req.protocol` and `req.get('host')` which don't work properly behind reverse proxies
2. **localStorage key mismatch**: The callback page was storing tokens with different keys than the dashboard expected
3. **Missing environment variables**: No production URL configuration

## Solution

### 1. Environment Variables Setup

Create a `.env` file in the root directory with the following variables:

```bash
# MongoDB Connection
MONGODB_URI=your_mongodb_connection_string_here

# JWT Secret Key
JWT_SECRET=your_jwt_secret_key_here

# Server Port
PORT=3000

# Environment Mode
NODE_ENV=production

# Production URL (REQUIRED for Google OAuth)
PRODUCTION_URL=https://legendbl.tech

# Google OAuth Redirect URI (REQUIRED)
GOOGLE_REDIRECT_URI=https://legendbl.tech/auth/google/callback
```

### 2. Google Cloud Console Configuration

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select your project
3. Navigate to **APIs & Services** > **Credentials**
4. Click on your OAuth 2.0 Client ID
5. Under **Authorized redirect URIs**, ensure you have:
   ```
   https://legendbl.tech/auth/google/callback
   ```
6. Save the changes

### 3. Code Changes Made

#### server.js
- Fixed redirect URI construction to use environment variables first
- Added proper fallback logic for production vs development
- Changed error handling in GET endpoint to redirect instead of JSON response
- Fixed ternary operator precedence issues

#### auth/google/callback.html
- Updated localStorage keys to match dashboard expectations:
  - `token` → `legendShopToken`
  - `user` → `legendShopUser`

#### .env.example
- Added PRODUCTION_URL variable
- Added GOOGLE_REDIRECT_URI variable

## How It Works Now

1. User clicks "Sign in with Google" on login.html or signup.html
2. User is redirected to Google's OAuth consent screen
3. After approval, Google redirects to: `https://legendbl.tech/auth/google/callback?code=...`
4. The server's GET endpoint at `/auth/google/callback` receives the request
5. Server exchanges the authorization code for an access token using the correct redirect URI
6. Server creates or finds the user in the database
7. Server generates a JWT token
8. Server redirects to dashboard with token in URL parameters
9. Dashboard stores the token in localStorage and displays the user information

## Testing

To test the OAuth flow:

1. Ensure all environment variables are set in `.env`
2. Start the server: `npm start`
3. Navigate to the login page
4. Click "Sign in with Google"
5. Complete the Google authentication
6. You should be redirected to the dashboard successfully

## Troubleshooting

### Still getting 404 error?
- Verify the redirect URI in Google Cloud Console matches exactly: `https://legendbl.tech/auth/google/callback`
- Check that environment variables are loaded: Add `console.log(process.env.GOOGLE_REDIRECT_URI)` in server.js
- Ensure the server is restarted after changing `.env`

### User is redirected but not logged in?
- Check browser console for localStorage errors
- Verify the token is being stored with key `legendShopToken`
- Check that dashboard.html is looking for `legendShopToken`

### Database errors?
- Ensure MongoDB connection string is correct in `.env`
- Check that MongoDB is running and accessible
- Review server logs for connection errors

## Important Notes

- The redirect URI must match EXACTLY between:
  1. Google Cloud Console configuration
  2. The `GOOGLE_REDIRECT_URI` in `.env`
  3. The client-side redirect URI in login.html and signup.html
- Always use HTTPS in production for OAuth
- Keep your Google Client Secret secure and never commit it to version control
- The client ID and secret in the code should be moved to environment variables in production

## Security Recommendations

1. Move Google Client ID and Client Secret to environment variables:
   ```bash
   GOOGLE_CLIENT_ID=your_client_id
   GOOGLE_CLIENT_SECRET=your_client_secret
   ```

2. Update server.js to use:
   ```javascript
   const GOOGLE_CLIENT_ID = process.env.GOOGLE_CLIENT_ID;
   const GOOGLE_CLIENT_SECRET = process.env.GOOGLE_CLIENT_SECRET;
   ```

3. Update login.html and signup.html to fetch the client ID from an API endpoint instead of hardcoding it
