# Legend Shop - Premium Shopping Website

A modern, secure, and feature-rich e-commerce authentication system with a beautiful animated UI.

## 🌟 Features

- **Secure Authentication**: Advanced user registration and login system with JWT tokens
- **MongoDB Integration**: Robust database management with Mongoose
- **Modern UI**: Beautiful gradient animations and glassmorphism design
- **Password Strength Checker**: Real-time password validation and strength indicator
- **CAPTCHA System**: Math-based CAPTCHA for bot prevention
- **Responsive Design**: Fully responsive across all devices
- **Form Validation**: Comprehensive client and server-side validation
- **Age Verification**: Ensures users are 13+ years old
- **Human Verification**: Simple checkbox verification system

## 🚀 Getting Started

### Prerequisites

- Node.js (v14 or higher)
- MongoDB Atlas account or local MongoDB installation
- npm or yarn package manager

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/legendhkek/LEGEND-SHOP-WEBSITE-.git
   cd LEGEND-SHOP-WEBSITE-
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Configure environment variables**
   
   The `.env` file is already configured with MongoDB connection. You can update it if needed:
   ```
   MONGODB_URI=your_mongodb_connection_string
   JWT_SECRET=your_jwt_secret_key
   PORT=3000
   NODE_ENV=development
   ```

4. **Start the server**
   ```bash
   npm start
   ```

   For development with auto-reload:
   ```bash
   npm run dev
   ```

5. **Access the website**
   
   Open your browser and navigate to:
   - Homepage: http://localhost:3000/index.html
   - Login: http://localhost:3000/login.html
   - Signup: http://localhost:3000/signup.html
   - Dashboard: http://localhost:3000/dashboard.html

## 📁 Project Structure

```
LEGEND-SHOP-WEBSITE-/
├── index.html          # Landing page with features showcase
├── login.html          # User login page
├── signup.html         # User registration page
├── dashboard.html      # User dashboard (after login)
├── styles.css          # Main stylesheet with animations
├── script.js           # Client-side JavaScript
├── server.js           # Express server with API routes
├── package.json        # Project dependencies
├── .env               # Environment variables (not in git)
└── .gitignore         # Git ignore rules
```

## 🎨 Design Features

- **Gradient Animations**: Dynamic multi-color gradient backgrounds
- **Glassmorphism**: Modern glass-effect cards with backdrop blur
- **3D Card Tilt**: Interactive card tilt effect on hover
- **Floating Elements**: Animated floating shapes in background
- **Smooth Transitions**: Butter-smooth animations and transitions
- **Form Animations**: Interactive form elements with visual feedback

## 🔒 Security Features

- Password hashing with bcrypt (12 salt rounds)
- JWT-based authentication with secret validation
- Input validation on both client and server
- Age verification (13+ required)
- CAPTCHA system for signup
- Human verification checkbox
- Protected routes
- CORS enabled for API security
- **Rate limiting**: 
  - Authentication endpoints: 10 requests per 15 minutes per IP
  - General API endpoints: 100 requests per 15 minutes per IP
- Environment variable validation for MongoDB URI and JWT_SECRET
- Admin endpoints disabled in production

### Security Considerations

- **Static File Serving**: Currently serves files from the root directory. In production, consider:
  - Moving HTML/CSS/JS files to a dedicated `public` directory
  - Using a reverse proxy (nginx) to serve static files
  - Ensuring sensitive files (.env, server.js, etc.) are not accessible
- The `/api/users` endpoint is disabled in production mode
- Always use HTTPS in production to protect credentials in transit

## 🛠️ API Endpoints

### Health Check
- **GET** `/api/health`
  - Returns server status and MongoDB connection state

### User Registration
- **POST** `/api/signup`
  - Body: `{ firstName, lastName, email, password, dateOfBirth }`
  - Returns: JWT token and user data

### User Login
- **POST** `/api/login`
  - Body: `{ email, password, humanVerified }`
  - Returns: JWT token and user data

### Get All Users (Admin)
- **GET** `/api/users`
  - Returns: List of all users (testing only)

## 🎯 Usage

### For Users

1. Start at the **index.html** landing page
2. Click "Get Started" to create a new account
3. Fill in the registration form with:
   - First name and last name
   - Date of birth (must be 13+)
   - Email address
   - Password (minimum 8 characters)
   - Solve the math CAPTCHA
   - Check the human verification box
   - Accept terms and conditions
4. After successful registration, you'll be redirected to login
5. Login with your credentials
6. Access your dashboard

### For Developers

1. The backend runs on Express.js with MongoDB
2. Frontend uses vanilla JavaScript (no frameworks)
3. Modify styles in `styles.css`
4. Update client logic in `script.js`
5. Extend API in `server.js`

## 📝 Scripts

- `npm start` - Start the production server
- `npm run dev` - Start development server with nodemon

## 🐛 Troubleshooting

### Backend not connecting
- Ensure MongoDB URI is correct in `.env`
- Check if MongoDB Atlas IP whitelist includes your IP
- Verify Node.js and npm are installed

### Frontend not loading
- Make sure server is running (`npm start`)
- Access via `http://localhost:3000` not `file://`
- Check browser console for errors

### CAPTCHA not showing
- Wait a few seconds for page to fully load
- The CAPTCHA generates automatically on page load
- Try refreshing the page

## 👨‍💻 Owner & Contact

- **Telegram**: [@legend_bl](https://t.me/legend_bl)
- **Email**: LEGENDXKEYGRID@GMAIL.COM

## 📄 License

ISC License

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

---

**Made with ❤️ by Legend Shop**
