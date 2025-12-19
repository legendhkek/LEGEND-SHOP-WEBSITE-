# Setting Up Admin Access

## Overview
The Legend Shop Tools system includes an admin panel for managing redeem codes and credits. This guide explains how to set up the first admin user.

## Creating an Admin User

### Option 1: Using MongoDB Compass (GUI)

1. Open MongoDB Compass and connect to your database
2. Navigate to your database (e.g., `legendshop`)
3. Open the `users` collection
4. Find your user document
5. Add or update the `isAdmin` field to `true`:
   ```json
   {
     "_id": ObjectId("..."),
     "firstName": "Your",
     "lastName": "Name",
     "email": "your@email.com",
     "isAdmin": true,
     "credits": 100
   }
   ```
6. Save the document

### Option 2: Using MongoDB Shell

1. Connect to your MongoDB instance:
   ```bash
   mongosh "your_mongodb_uri"
   ```

2. Switch to your database:
   ```bash
   use legendshop
   ```

3. Update the user to be an admin:
   ```bash
   db.users.updateOne(
     { email: "your@email.com" },
     { $set: { isAdmin: true } }
   )
   ```

### Option 3: Using Node.js Script

Create a file `make-admin.js`:

```javascript
require('dotenv').config();
const mongoose = require('mongoose');

mongoose.connect(process.env.MONGODB_URI)
  .then(async () => {
    const User = mongoose.model('User', new mongoose.Schema({}, { strict: false }));
    
    const email = process.argv[2];
    if (!email) {
      console.error('Usage: node make-admin.js <email>');
      process.exit(1);
    }
    
    const result = await User.updateOne(
      { email },
      { $set: { isAdmin: true } }
    );
    
    if (result.modifiedCount > 0) {
      console.log(`✅ ${email} is now an admin`);
    } else {
      console.log(`❌ User ${email} not found`);
    }
    
    process.exit(0);
  })
  .catch(err => {
    console.error('Error:', err);
    process.exit(1);
  });
```

Run it:
```bash
node make-admin.js your@email.com
```

## Accessing the Admin Panel

Once you have admin access:

1. Log in to your account
2. Navigate to the dashboard
3. Look for the "Admin" section in the sidebar
4. Click "Admin Panel" to access the management interface

## Admin Features

### Generate Redeem Codes
- Set custom credit amounts (1-10000 credits)
- Optional expiration dates (1-365 days)
- Unique, randomly generated codes

### Manage Codes
- View all generated codes
- See usage status (Active/Used/Expired)
- Track who redeemed each code
- Delete unused codes

### Monitor Activity
- View total codes generated
- Track active vs used codes
- Monitor total credits distributed

## Security Notes

⚠️ **Important Security Considerations:**

1. **Keep Admin Status Secure**: Only trusted users should have admin access
2. **Regular Audits**: Periodically review who has admin access
3. **Code Distribution**: Share redeem codes securely with users
4. **Expiration Dates**: Use expiration dates for promotional codes
5. **Monitor Usage**: Regularly check the admin panel for unusual activity

## Troubleshooting

### "You do not have admin access" error
- Verify the `isAdmin` field is set to `true` in the database
- Log out and log back in to refresh your session
- Check that you're logged in with the correct account

### Admin section not showing in dashboard
- Ensure `isAdmin: true` is set in your user document
- Clear browser cache and reload
- Check browser console for JavaScript errors

### Can't generate codes
- Verify MongoDB connection is active
- Check server logs for error messages
- Ensure credits and expiration values are within valid ranges

## Best Practices

1. **Credit Amounts**: Set reasonable credit amounts based on your pricing
2. **Expiration**: Use short expiration dates for promotional codes
3. **Documentation**: Keep track of codes you've distributed
4. **Regular Cleanup**: Delete unused expired codes periodically
5. **Backup**: Regular backup your database including the codes collection

## Support

For issues or questions:
- Telegram: [@legend_bl](https://t.me/legend_bl)
- Email: LEGENDXKEYGRID@GMAIL.COM
