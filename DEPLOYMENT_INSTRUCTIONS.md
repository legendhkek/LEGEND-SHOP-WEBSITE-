# Deployment Instructions for Google OAuth Fix

## Quick Setup for Production

To deploy the Google OAuth fix to your production server, follow these steps:

### 1. Update Environment Variables

Create or update your `.env` file on the production server with these **required** variables:

```bash
# Add these to your existing .env file
PRODUCTION_URL=https://legendbl.tech
GOOGLE_REDIRECT_URI=https://legendbl.tech/auth/google/callback
```

### 2. Restart the Server

After updating the `.env` file, restart your Node.js server:

```bash
# If using pm2
pm2 restart legend-shop

# If using systemd
sudo systemctl restart legend-shop

# If running directly
# Stop the current process and start again
node server.js
```

### 3. Verify Google Cloud Console Configuration

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Navigate to **APIs & Services** > **Credentials**
3. Click on your OAuth 2.0 Client ID
4. Verify the redirect URI is exactly: `https://legendbl.tech/auth/google/callback`
5. If not present, add it and save

### 4. Test the Flow

1. Clear your browser cache and localStorage
2. Go to `https://legendbl.tech/login.html`
3. Click "Sign in with Google"
4. Complete the Google authentication
5. You should be redirected to the dashboard successfully

## What Was Fixed

### Technical Changes

1. **server.js (Lines 567-645)**
   - Fixed redirect URI to use environment variables
   - Changed error response from JSON to redirect
   - Added proper fallback logic for development/production

2. **auth/google/callback.html (Lines 223-226, 262-265)**
   - Updated localStorage keys to match dashboard expectations
   - Changed `token` → `legendShopToken`
   - Changed `user` → `legendShopUser`

3. **.env.example**
   - Added `PRODUCTION_URL` variable
   - Added `GOOGLE_REDIRECT_URI` variable

### Authentication Flow After Fix

```
1. User clicks "Sign in with Google"
   ↓
2. Redirected to Google OAuth (consent screen)
   ↓
3. User approves and Google redirects to:
   https://legendbl.tech/auth/google/callback?code=...
   ↓
4. Server's GET endpoint receives the callback
   ↓
5. Server exchanges code for access token
   (uses GOOGLE_REDIRECT_URI from .env)
   ↓
6. Server creates/finds user in database
   ↓
7. Server generates JWT token
   ↓
8. Server redirects to:
   /dashboard.html?token=...&user=...
   ↓
9. Dashboard stores token in localStorage
   (as legendShopToken and legendShopUser)
   ↓
10. User sees dashboard with their information
```

## Common Issues After Deployment

### Issue: Still getting 404
**Solution**: 
- Verify `.env` file is in the correct location
- Check that the server was restarted after updating `.env`
- Verify `GOOGLE_REDIRECT_URI` in `.env` matches Google Console

### Issue: "Database not connected" error
**Solution**:
- Check MongoDB connection string in `.env`
- Verify MongoDB server is running and accessible
- Check server logs for connection errors

### Issue: User redirected but not logged in
**Solution**:
- Clear browser cache and localStorage
- Open browser console and check for errors
- Verify token is being stored with key `legendShopToken`

### Issue: OAuth error from Google
**Solution**:
- Verify Google Client ID and Secret are correct
- Check that redirect URI in Google Console matches exactly
- Ensure the OAuth consent screen is properly configured

## Files Changed

- ✅ `server.js` - OAuth endpoint fixes
- ✅ `auth/google/callback.html` - localStorage key updates
- ✅ `.env.example` - New environment variables
- ✅ `GOOGLE_OAUTH_SETUP.md` - Setup guide
- ✅ `DEPLOYMENT_INSTRUCTIONS.md` - This file

## Rollback Plan

If something goes wrong, you can rollback by:

1. Revert the changes:
   ```bash
   git checkout <previous-commit-hash>
   ```

2. Or manually revert `.env`:
   ```bash
   # Remove these lines from .env
   # PRODUCTION_URL=https://legendbl.tech
   # GOOGLE_REDIRECT_URI=https://legendbl.tech/auth/google/callback
   ```

3. Restart the server

## Support

For detailed setup instructions, see [GOOGLE_OAUTH_SETUP.md](./GOOGLE_OAUTH_SETUP.md)

For troubleshooting, check the "Troubleshooting" section in GOOGLE_OAUTH_SETUP.md
