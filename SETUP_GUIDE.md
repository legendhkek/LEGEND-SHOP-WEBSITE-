# Legend Shop - Quick Setup Guide

## Problem: "Backend is OFF" Error on Login/Signup

If you're seeing an error message saying "Backend is OFF, run npm start" when trying to login or signup, this guide will help you fix it.

## Prerequisites

Before starting, make sure you have:
- Node.js installed (v14 or higher) - [Download here](https://nodejs.org/)
- A MongoDB database (see options below)

## Setup Steps

### Step 1: Install Dependencies

Open a terminal in the project directory and run:

```bash
npm install
```

### Step 2: Configure MongoDB

You have two options:

#### Option A: MongoDB Atlas (FREE & Recommended)

1. Go to [MongoDB Atlas](https://www.mongodb.com/cloud/atlas)
2. Create a free account
3. Create a new cluster (select M0 FREE tier)
4. Click "Connect" → "Connect your application"
5. Copy the connection string (it looks like: `mongodb+srv://username:password@cluster0.xxxxx.mongodb.net/`)
6. Open `.env` file in the project root
7. Replace the `MONGODB_URI` value with your connection string:
   ```
   MONGODB_URI=mongodb+srv://yourusername:yourpassword@cluster0.xxxxx.mongodb.net/legendshop
   ```
8. Make sure to replace `<password>` with your actual password

#### Option B: Local MongoDB

1. Download and install MongoDB Community Server from [MongoDB Download Center](https://www.mongodb.com/try/download/community)
2. Start MongoDB service:
   - **Windows**: MongoDB service starts automatically
   - **Mac**: `brew services start mongodb-community`
   - **Linux**: `sudo systemctl start mongod`
3. The `.env` file is already configured for local MongoDB:
   ```
   MONGODB_URI=mongodb://localhost:27017/legendshop
   ```

### Step 3: Start the Backend Server

Run the following command in your terminal:

```bash
npm start
```

You should see:
```
🚀 Legend Shop Server running on http://localhost:3000
📝 API Endpoints:
   - POST http://localhost:3000/api/signup
   - POST http://localhost:3000/api/login
   - GET  http://localhost:3000/api/users
✅ MongoDB Connected Successfully!
```

### Step 4: Access the Website

Open your browser and go to:
- **Main page**: http://localhost:3000/index.php
- **Login**: http://localhost:3000/login.html
- **Signup**: http://localhost:3000/signup.html

**IMPORTANT**: Don't open the HTML files directly (file://). Always use http://localhost:3000/

## Troubleshooting

### Error: "Cannot find module"
**Solution**: Run `npm install` to install all dependencies

### Error: "MongoDB Connection Error"
**Solution**: 
- Check your MongoDB connection string in `.env`
- Make sure MongoDB is running (if using local)
- Verify your username/password (if using Atlas)
- Add your IP address to MongoDB Atlas whitelist

### Error: "Port 3000 is already in use"
**Solution**: 
- Close any other application using port 3000
- Or change the PORT in `.env` file to a different number (e.g., 3001)

### Error: "JWT_SECRET is not defined"
**Solution**: Make sure the `.env` file exists in the project root directory

### Login/Signup still not working
**Solutions**:
1. Check browser console (F12) for error messages
2. Make sure the server is running (`npm start`)
3. Clear browser cache and cookies
4. Try in incognito/private mode

## Quick Test

To verify everything is working:

1. Start the server: `npm start`
2. Open another terminal and run:
   ```bash
   curl http://localhost:3000/api/health
   ```
3. You should see:
   ```json
   {
     "status": "OK",
     "message": "Legend Shop Server Running",
     "mongodb": "Connected"
   }
   ```

If `mongodb` shows "Connected", you're all set! ✅

## Development Mode

For development with auto-reload (automatically restarts when you change code):

```bash
npm run dev
```

## Need Help?

Contact:
- **Telegram**: [@legend_bl](https://t.me/legend_bl)
- **Email**: LEGENDXKEYGRID@GMAIL.COM

## Security Notes

- Never commit the `.env` file to GitHub (it's already in `.gitignore`)
- Change the `JWT_SECRET` to a random secure string for production
- Use HTTPS in production
- Set `NODE_ENV=production` in production deployments
