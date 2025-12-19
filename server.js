const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const { body, validationResult } = require('express-validator');
const rateLimit = require('express-rate-limit');
const axios = require('axios');
require('dotenv').config();

// Google OAuth Configuration
const GOOGLE_CLIENT_ID = '674654993812-krpej9648d2205dqpls1dsq7tuhvlbft.apps.googleusercontent.com';
const GOOGLE_CLIENT_SECRET = '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';

const app = express();

// Rate limiting middleware
const authLimiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 10, // Limit each IP to 10 requests per windowMs
    message: 'Too many requests from this IP, please try again later.',
    standardHeaders: true,
    legacyHeaders: false,
});

const apiLimiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 100, // Limit each IP to 100 requests per windowMs
    message: 'Too many requests from this IP, please try again later.',
    standardHeaders: true,
    legacyHeaders: false,
});

// Middleware
app.use(cors());
app.use(express.json());
// Serve static files from a 'public' directory instead of root for security
// For now, keeping root but this should be changed in production
app.use(express.static(__dirname));

// MongoDB Connection
if (!process.env.MONGODB_URI) {
    console.error('❌ MONGODB_URI is not defined in .env file');
    console.log('⚠️  Server will continue without database (UI testing mode)');
} else {
    mongoose.connect(process.env.MONGODB_URI)
    .then(() => console.log('✅ MongoDB Connected Successfully!'))
    .catch(err => {
        console.error('❌ MongoDB Connection Error:', err.message);
        console.log('⚠️  Server will continue without database (UI testing mode)');
    });
}

// Handle unhandled promise rejections
process.on('unhandledRejection', (err) => {
    console.error('❌ Unhandled Rejection:', err);
});

// Handle uncaught exceptions
process.on('uncaughtException', (err) => {
    console.error('❌ Uncaught Exception:', err);
});

// User Schema
const userSchema = new mongoose.Schema({
    firstName: {
        type: String,
        required: true,
        trim: true
    },
    lastName: {
        type: String,
        required: true,
        trim: true
    },
    email: {
        type: String,
        required: true,
        unique: true,
        lowercase: true,
        trim: true
    },
    password: {
        type: String,
        required: true
    },
    dateOfBirth: {
        type: Date,
        required: true
    },
    googleId: {
        type: String,
        unique: true,
        sparse: true
    },
    createdAt: {
        type: Date,
        default: Date.now
    },
    lastLogin: {
        type: Date
    }
});

// Hash password before saving
userSchema.pre('save', async function(next) {
    if (!this.isModified('password')) return next();
    this.password = await bcrypt.hash(this.password, 12);
    next();
});

const User = mongoose.model('User', userSchema);

// ===== MIDDLEWARE =====

// MongoDB connection check middleware
const requireMongoConnection = (req, res, next) => {
    if (mongoose.connection.readyState !== 1) {
        // For OAuth callbacks, redirect instead of JSON response
        if (req.path.includes('/auth/google/callback')) {
            return res.redirect('/login.html?error=database_unavailable');
        }
        return res.status(503).json({
            success: false,
            message: 'Database is not connected. Please ensure MongoDB is running and properly configured in .env file.'
        });
    }
    next();
};

// ===== API ROUTES =====

// Health check
app.get('/api/health', (req, res) => {
    res.json({ 
        status: 'OK', 
        message: 'Legend Shop Server Running',
        mongodb: mongoose.connection.readyState === 1 ? 'Connected' : 'Disconnected'
    });
});

// Sign Up Route
app.post('/api/signup', authLimiter, requireMongoConnection, [
    body('firstName').trim().isLength({ min: 2 }).withMessage('First name must be at least 2 characters'),
    body('lastName').trim().isLength({ min: 2 }).withMessage('Last name must be at least 2 characters'),
    body('email').isEmail().normalizeEmail().withMessage('Invalid email address'),
    body('password').isLength({ min: 8 }).withMessage('Password must be at least 8 characters'),
    body('dateOfBirth').isISO8601().withMessage('Invalid date of birth')
], async (req, res) => {
    try {
        // Validate request
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            return res.status(400).json({ 
                success: false, 
                errors: errors.array() 
            });
        }

        const { firstName, lastName, email, password, dateOfBirth } = req.body;

        // Check if user already exists
        const existingUser = await User.findOne({ email });
        if (existingUser) {
            return res.status(400).json({ 
                success: false, 
                message: 'Email already registered' 
            });
        }

        // Validate age (must be 13+)
        const birthDate = new Date(dateOfBirth);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        if (age < 13) {
            return res.status(400).json({ 
                success: false, 
                message: 'You must be at least 13 years old to register' 
            });
        }

        // Create new user
        const user = new User({
            firstName,
            lastName,
            email,
            password,
            dateOfBirth: birthDate
        });

        await user.save();

        // Generate JWT token
        if (!process.env.JWT_SECRET) {
            throw new Error('JWT_SECRET is not defined in environment variables');
        }
        const token = jwt.sign(
            { userId: user._id, email: user.email },
            process.env.JWT_SECRET,
            { expiresIn: '7d' }
        );

        res.status(201).json({
            success: true,
            message: 'Account created successfully!',
            token,
            user: {
                id: user._id,
                firstName: user.firstName,
                lastName: user.lastName,
                email: user.email
            }
        });

    } catch (error) {
        console.error('Signup Error:', error);
        res.status(500).json({ 
            success: false, 
            message: 'Server error during registration' 
        });
    }
});

// Login Route
app.post('/api/login', authLimiter, requireMongoConnection, [
    body('email').isEmail().normalizeEmail().withMessage('Invalid email address'),
    body('password').notEmpty().withMessage('Password is required'),
    body('humanVerified').optional().isBoolean().withMessage('Invalid human verification flag')
], async (req, res) => {
    try {
        // Validate request
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            return res.status(400).json({ 
                success: false, 
                errors: errors.array() 
            });
        }

        const { email, password } = req.body;

        // Basic human check (client-side checkbox). Note: this is not a real captcha.
        if (req.body.humanVerified === false) {
            return res.status(400).json({
                success: false,
                message: 'Please confirm you are human'
            });
        }

        // Find user
        const user = await User.findOne({ email });
        if (!user) {
            return res.status(401).json({ 
                success: false, 
                message: 'Invalid email or password' 
            });
        }

        // Verify password
        const isPasswordValid = await bcrypt.compare(password, user.password);
        if (!isPasswordValid) {
            return res.status(401).json({ 
                success: false, 
                message: 'Invalid email or password' 
            });
        }

        // Update last login
        user.lastLogin = new Date();
        await user.save();

        // Generate JWT token
        if (!process.env.JWT_SECRET) {
            throw new Error('JWT_SECRET is not defined in environment variables');
        }
        const token = jwt.sign(
            { userId: user._id, email: user.email },
            process.env.JWT_SECRET,
            { expiresIn: '7d' }
        );

        res.json({
            success: true,
            message: 'Login successful!',
            token,
            user: {
                id: user._id,
                firstName: user.firstName,
                lastName: user.lastName,
                email: user.email,
                lastLogin: user.lastLogin
            }
        });

    } catch (error) {
        console.error('Login Error:', error);
        res.status(500).json({ 
            success: false, 
            message: 'Server error during login' 
        });
    }
});

// Get all users (Admin route - for testing only, DISABLE IN PRODUCTION)
// WARNING: This endpoint should be protected or removed in production
app.get('/api/users', apiLimiter, async (req, res) => {
    // Only allow in development mode
    if (process.env.NODE_ENV === 'production') {
        return res.status(403).json({ 
            success: false, 
            message: 'This endpoint is disabled in production' 
        });
    }
    
    try {
        const users = await User.find().select('-password');
        res.json({
            success: true,
            count: users.length,
            users
        });
    } catch (error) {
        console.error('Error fetching users:', error);
        res.status(500).json({ 
            success: false, 
            message: 'Server error' 
        });
    }
});

// Google OAuth Callback Route
app.get('/auth/google/callback', requireMongoConnection, async (req, res) => {
    try {
        const { code } = req.query;
        
        if (!code) {
            return res.status(400).json({
                success: false,
                message: 'Authorization code not provided'
            });
        }
        
        // Exchange authorization code for access token
        const tokenResponse = await axios.post('https://oauth2.googleapis.com/token', {
            code,
            client_id: GOOGLE_CLIENT_ID,
            client_secret: GOOGLE_CLIENT_SECRET,
            redirect_uri: `${req.protocol}://${req.get('host')}/auth/google/callback`,
            grant_type: 'authorization_code'
        });
        
        const { access_token } = tokenResponse.data;
        
        // Get user info from Google
        const userInfoResponse = await axios.get('https://www.googleapis.com/oauth2/v2/userinfo', {
            headers: {
                Authorization: `Bearer ${access_token}`
            }
        });
        
        const googleUser = userInfoResponse.data;
        
        // Check if user exists in database
        let user = await User.findOne({ email: googleUser.email });
        
        if (!user) {
            // Create new user
            const nameParts = googleUser.name.split(' ');
            user = new User({
                firstName: nameParts[0] || 'User',
                lastName: nameParts.slice(1).join(' ') || 'Account',
                email: googleUser.email,
                password: await bcrypt.hash(Math.random().toString(36), 12), // Random password for OAuth users
                dateOfBirth: new Date('2000-01-01'), // Default DOB for OAuth users
                googleId: googleUser.id
            });
            await user.save();
        }
        
        // Update last login
        user.lastLogin = new Date();
        await user.save();
        
        // Generate JWT token
        if (!process.env.JWT_SECRET) {
            throw new Error('JWT_SECRET is not defined in environment variables');
        }
        const token = jwt.sign(
            { userId: user._id, email: user.email },
            process.env.JWT_SECRET,
            { expiresIn: '7d' }
        );
        
        // Redirect to dashboard with token
        res.redirect(`/dashboard.html?token=${token}&user=${encodeURIComponent(JSON.stringify({
            id: user._id,
            firstName: user.firstName,
            lastName: user.lastName,
            email: user.email
        }))}`);
        
    } catch (error) {
        console.error('Google OAuth Error:', error);
        res.redirect('/login.html?error=oauth_failed');
    }
});

// Start Server
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`🚀 Legend Shop Server running on http://localhost:${PORT}`);
    console.log(`📝 API Endpoints:`);
    console.log(`   - POST http://localhost:${PORT}/api/signup`);
    console.log(`   - POST http://localhost:${PORT}/api/login`);
    console.log(`   - GET  http://localhost:${PORT}/api/users`);
});
