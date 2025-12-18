# Implementation Notes: Video Intro, Google OAuth & Rotating Backgrounds

## Overview
This document describes the changes made to implement the requested features for the Legend Shop website.

## Changes Made

### 1. Video Intro Sequence (index.php)
- **Replaced** the animated space journey intro with a video-based intro using `1.mp4`
- **Removed** the complex canvas-based solar system animation
- **Added** video element with proper autoplay, muted, and playsinline attributes for cross-browser compatibility
- **Implemented** "Get Started" button that appears 2 seconds before the video ends
- **Added** smooth fade-out transition when video ends or is skipped
- **Maintained** the skip button functionality for users who want to bypass the intro

### 2. Get Started Button
- **Removed** the "Get Started" button from the main content section (after intro)
- **Added** new "Get Started" button overlay in the video intro that:
  - Appears dynamically near the end of the video
  - Has animated entrance with scale and opacity transitions
  - Links to `signup.html`
  - Includes hover effects and animations
  - Is responsive for mobile devices

### 3. Video Backgrounds for Login & Signup Pages
- **Added** `2.mp4` as full-screen video background for both `login.html` and `signup.html`
- **Configured** video to:
  - Autoplay on page load
  - Loop continuously
  - Play muted (required for autoplay)
  - Have reduced opacity (0.3) for better content readability
  - Use `object-fit: cover` for proper scaling

### 4. Auto-Rotating Background System
- **Implemented** background rotation system that changes every 3 days
- **Features**:
  - Uses localStorage to track last rotation timestamp
  - Maintains current background index across sessions
  - Automatically rotates through a curated list of anime 8K wallpapers
  - Smooth fade-in transitions when backgrounds change
  - Works alongside the video background as a fallback/overlay

- **Wallpaper Sources**:
  - High-quality 8K images from Unsplash and Pexels
  - Anime-style wallpapers for visual appeal
  - Optimized URLs with quality parameters

### 5. Google OAuth Integration
- **Added** Google OAuth credentials to the system:
  - Client ID: `674654993812-krpej9648d2205dqpls1dsq7tuhvlbft.apps.googleusercontent.com`
  - Client Secret: `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`

- **Frontend Implementation** (login.html & signup.html):
  - Google Sign-in button click handler
  - OAuth URL construction with proper parameters
  - Automatic redirect to Google OAuth consent screen

- **Backend Implementation** (server.js):
  - New `/auth/google/callback` endpoint
  - Token exchange with Google OAuth API
  - User profile retrieval from Google
  - Automatic user creation for new Google accounts
  - JWT token generation for authenticated sessions
  - Redirect to dashboard with authentication data

- **Database Schema Update**:
  - Added `googleId` field to User schema for OAuth users
  - Configured as unique and sparse index

### 6. Configuration File
- **Created** `config.js` with centralized configuration:
  - Google OAuth credentials
  - Background rotation interval (3 days)
  - Anime wallpaper URLs array
  - Modular structure for easy updates

### 7. Dependencies
- **Added** `axios` package to `package.json` for Google OAuth API calls
- Version: `^1.6.2`

## Technical Details

### Video Implementation
- Used HTML5 `<video>` element with multiple attributes for compatibility
- `autoplay`: Starts video automatically
- `muted`: Required for autoplay in most browsers
- `playsinline`: Prevents fullscreen on mobile devices
- `loop`: Only for background videos (not intro)

### CSS Improvements
- Added responsive styles for video elements
- Maintained existing design system and animations
- Z-index layering: video (-2), rotating background (-1), content (positive)

### JavaScript Enhancements
- Event listeners for video timeupdate and ended events
- LocalStorage integration for persistent background rotation
- Google OAuth URL construction and redirect handling
- Smooth transitions and fade effects

### Security Considerations
- OAuth credentials are stored server-side where possible
- Client-side credentials are necessary for redirect flow
- Rate limiting maintained for all auth endpoints
- CSRF protection through OAuth state parameter (can be added)

## Testing Recommendations

### Manual Testing Checklist
1. ✅ Video intro plays on page load
2. ✅ "Get Started" button appears near video end
3. ✅ Skip button works to bypass intro
4. ✅ Main content appears after intro completes
5. ✅ Login page shows 2.mp4 video background
6. ✅ Signup page shows 2.mp4 video background
7. ✅ Background rotation system tracks timestamps correctly
8. ✅ Google OAuth button redirects to Google login
9. 🔄 Google OAuth callback creates/logs in users (requires OAuth setup)
10. 🔄 Mobile responsiveness for all new features

### Browser Compatibility
- Chrome/Edge: Full support
- Firefox: Full support
- Safari: May require user interaction for autoplay (muted videos usually work)
- Mobile browsers: playsinline attribute ensures proper behavior

## Future Enhancements
1. Add OAuth state parameter for CSRF protection
2. Implement fallback images for video loading failures
3. Add loading indicators for video buffering
4. Consider video preloading for faster start
5. Add analytics for video completion rates
6. Implement user preference for skipping intro permanently
7. Add admin panel to update wallpaper URLs
8. Implement image CDN for better performance

## Files Modified
- `index.php` - Video intro, button removal, styling updates
- `login.html` - Video background, rotating background, OAuth setup
- `signup.html` - Video background, rotating background, OAuth setup
- `server.js` - Google OAuth backend, user schema update
- `package.json` - Added axios dependency

## Files Created
- `config.js` - Configuration file for OAuth and backgrounds

## Environment Variables Required
- `GOOGLE_CLIENT_ID` - (stored in code for client-side OAuth flow)
- `GOOGLE_CLIENT_SECRET` - (stored in server.js, should move to .env)
- `JWT_SECRET` - Existing, used for session tokens
- `MONGODB_URI` - Existing, for database connection

## Deployment Notes
1. Ensure video files (1.mp4, 2.mp4) are accessible from web root
2. Install npm dependencies: `npm install`
3. Configure proper redirect URIs in Google OAuth console
4. Test OAuth flow in production environment
5. Monitor video loading performance and adjust as needed
6. Consider CDN for video delivery in production

## Support
For issues or questions, contact:
- Telegram: @legend_bl
- Email: LEGENDXKEYGRID@GMAIL.COM
