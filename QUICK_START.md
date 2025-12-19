# Quick Start Guide - LEGEND CHECKER

## 🚀 Getting Started

### Step 1: Start the Server

```bash
# Install dependencies (first time only)
npm install

# Start the server
npm start
```

The server will start at `http://localhost:3000`

### Step 2: Create an Account

1. Navigate to `http://localhost:3000/signup.html`
2. Fill in your information
3. Complete the signup process
4. You'll automatically get 100 credits!

### Step 3: Access LEGEND CHECKER

1. Log in at `http://localhost:3000/login.html`
2. Go to dashboard at `http://localhost:3000/dashboard.html`
3. Click on "Tools" in the sidebar
4. Click on "LEGEND CHECKER" (the recommended tool)

### Step 4: Use the Tools

#### View Your Credits
- Your credits are displayed at the top of LEGEND CHECKER
- Default: 100 credits on signup

#### Redeem a Code
1. Click "Redeem Code" button
2. Enter your redeem code (format: XXXXXXXX-XXXXXXXX)
3. Click "Redeem"
4. Credits will be added to your account

#### Check the Vault
- View your saved cards in the Vault section
- See card details, status, and BIN information
- Copy or delete cards as needed

### Step 5: Admin Setup (Optional)

If you want to generate redeem codes:

1. Set yourself as admin in MongoDB:
   ```javascript
   db.users.updateOne(
     { email: "your@email.com" },
     { $set: { isAdmin: true } }
   )
   ```

2. Log out and log back in

3. Navigate to `http://localhost:3000/tools/admin.html`

4. Generate redeem codes with custom credits

See `ADMIN_SETUP.md` for detailed instructions.

## 📱 Mobile Access

The site is fully optimized for mobile devices:
- Open on your phone's browser
- All features work smoothly
- Touch-optimized interface
- No lag or performance issues

## 🎯 Key Features

### For Users
- ✅ 100 free credits on signup
- ✅ Redeem codes for more credits
- ✅ Multiple checker gateways
- ✅ Vault for saved cards
- ✅ Transaction history
- ✅ Beautiful UI

### For Admins
- ✅ Generate redeem codes
- ✅ Set custom credit amounts (1-10000)
- ✅ Set expiration dates (1-365 days)
- ✅ View all codes and usage
- ✅ Monitor statistics

## 🔧 Troubleshooting

### Backend Not Starting
```bash
# Make sure you have Node.js installed
node --version

# Install dependencies
npm install

# Check if MongoDB is configured (optional)
# The system works without MongoDB for UI testing
```

### Can't Login
- Make sure the server is running
- Check you're accessing via `http://localhost:3000`
- Not via `file://` protocol

### Redeem Code Not Working
- Check the code format: XXXXXXXX-XXXXXXXX
- Codes are case-insensitive
- Each code can only be used once
- Rate limit: 5 attempts per hour

### Admin Panel Not Showing
- Make sure `isAdmin: true` is set in your user document
- Log out and log back in
- Check MongoDB connection

## 📞 Support

For help or questions:
- **Telegram**: [@legend_bl](https://t.me/legend_bl)
- **Email**: LEGENDXKEYGRID@GMAIL.COM

## 📖 More Information

- **Full Implementation Details**: See `IMPLEMENTATION_COMPLETE.md`
- **Admin Setup**: See `ADMIN_SETUP.md`
- **Tools Documentation**: See `tools/README.md`

## 🎉 Enjoy Using LEGEND CHECKER!

Everything is set up and ready to use. The tool is:
- ✅ Fully functional
- ✅ Secure
- ✅ Mobile-optimized
- ✅ Production-ready

Start checking and managing your credits now! 🚀
