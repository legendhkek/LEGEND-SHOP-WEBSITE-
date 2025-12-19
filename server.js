const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const { body, validationResult } = require('express-validator');
const rateLimit = require('express-rate-limit');
const axios = require('axios');
const fs = require('fs').promises;
const path = require('path');
const multer = require('multer');
const crypto = require('crypto');
require('dotenv').config();

// File paths for JSON storage
const USERS_FILE = path.join(__dirname, 'users.json');
const UPLOADS_DIR = path.join(__dirname, 'uploads');
const PROFILE_PICS_DIR = path.join(UPLOADS_DIR, 'profile-pics');

// Initialize storage directories
async function initializeStorage() {
    try {
        await fs.mkdir(UPLOADS_DIR, { recursive: true });
        await fs.mkdir(PROFILE_PICS_DIR, { recursive: true });
        
        // Initialize users.json if it doesn't exist
        try {
            await fs.access(USERS_FILE);
        } catch {
            await fs.writeFile(USERS_FILE, JSON.stringify({ users: [] }, null, 2));
            console.log('✅ Created users.json file');
        }
    } catch (error) {
        console.error('Error initializing storage:', error);
    }
}

// Helper functions for users.json
async function readUsers() {
    try {
        const data = await fs.readFile(USERS_FILE, 'utf8');
        return JSON.parse(data);
    } catch (error) {
        return { users: [] };
    }
}

async function writeUsers(data) {
    await fs.writeFile(USERS_FILE, JSON.stringify(data, null, 2));
}

// Configure multer for profile picture uploads
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, PROFILE_PICS_DIR);
    },
    filename: (req, file, cb) => {
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        cb(null, 'profile-' + uniqueSuffix + path.extname(file.originalname));
    }
});

const upload = multer({
    storage: storage,
    limits: { fileSize: 5 * 1024 * 1024 }, // 5MB limit
    fileFilter: (req, file, cb) => {
        const allowedTypes = /jpeg|jpg|png|gif/;
        const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
        const mimetype = allowedTypes.test(file.mimetype);
        
        if (extname && mimetype) {
            return cb(null, true);
        }
        cb(new Error('Only image files are allowed!'));
    }
});

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

// Rate limiter for redeem code attempts (prevent brute force)
const redeemLimiter = rateLimit({
    windowMs: 60 * 60 * 1000, // 1 hour
    max: 5, // Limit each IP to 5 redeem attempts per hour
    message: 'Too many redeem attempts, please try again later.',
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
    },
    credits: {
        type: Number,
        default: 100
    },
    isAdmin: {
        type: Boolean,
        default: false
    },
    role: {
        type: String,
        enum: ['free', 'premium', 'admin', 'owner'],
        default: 'free'
    },
    checkLimit: {
        type: Number,
        default: 1000 // free: 1000, premium: 5000, admin: 10000, owner: unlimited
    }
});

// Hash password before saving
userSchema.pre('save', async function(next) {
    if (!this.isModified('password')) return next();
    this.password = await bcrypt.hash(this.password, 12);
    next();
});

const User = mongoose.model('User', userSchema);

// Redeem Code Schema
const redeemCodeSchema = new mongoose.Schema({
    code: {
        type: String,
        required: true,
        unique: true,
        uppercase: true
    },
    credits: {
        type: Number,
        required: true
    },
    isActive: {
        type: Boolean,
        default: true
    },
    usedBy: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User'
    },
    usedAt: {
        type: Date
    },
    createdBy: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User',
        required: true
    },
    createdAt: {
        type: Date,
        default: Date.now
    },
    expiresAt: {
        type: Date
    }
});

const RedeemCode = mongoose.model('RedeemCode', redeemCodeSchema);

// Credit Transaction Schema
const creditTransactionSchema = new mongoose.Schema({
    userId: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User',
        required: true
    },
    amount: {
        type: Number,
        required: true
    },
    type: {
        type: String,
        enum: ['redeem', 'purchase', 'spend', 'admin'],
        required: true
    },
    description: {
        type: String
    },
    redeemCode: {
        type: String
    },
    createdAt: {
        type: Date,
        default: Date.now
    }
});

const CreditTransaction = mongoose.model('CreditTransaction', creditTransactionSchema);

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

// JWT Authentication Middleware
const authenticateToken = (req, res, next) => {
    const authHeader = req.headers['authorization'];
    const token = authHeader && authHeader.split(' ')[1];

    if (!token) {
        return res.status(401).json({
            success: false,
            message: 'Access token required'
        });
    }

    try {
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        req.userId = decoded.userId;
        req.userEmail = decoded.email;
        next();
    } catch (error) {
        return res.status(403).json({
            success: false,
            message: 'Invalid or expired token'
        });
    }
};

// Admin check middleware
const requireAdmin = async (req, res, next) => {
    try {
        const user = await User.findById(req.userId);
        if (!user || !user.isAdmin) {
            return res.status(403).json({
                success: false,
                message: 'Admin access required'
            });
        }
        next();
    } catch (error) {
        return res.status(500).json({
            success: false,
            message: 'Error checking admin status'
        });
    }
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

        // Also save to users.json for file-based access
        try {
            const usersData = await readUsers();
            const hashedPassword = await bcrypt.hash(password, 10);
            usersData.users.push({
                id: user._id.toString(),
                firstName,
                lastName,
                email,
                password: hashedPassword,
                dateOfBirth: birthDate.toISOString(),
                profilePicture: null,
                createdAt: new Date().toISOString()
            });
            await writeUsers(usersData);
            console.log('✅ User saved to users.json');
        } catch (jsonError) {
            console.log('⚠️  Failed to save to users.json:', jsonError.message);
        }

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
                email: user.email,
                isAdmin: user.isAdmin || false,
                credits: user.credits || 100
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
                lastLogin: user.lastLogin,
                isAdmin: user.isAdmin || false,
                credits: user.credits || 100
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
app.get('/auth/google/callback', authLimiter, requireMongoConnection, async (req, res) => {
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

// POST endpoint for Google OAuth callback (used by callback page)
app.post('/api/auth/google/callback', authLimiter, requireMongoConnection, async (req, res) => {
    try {
        const { code, redirectUri } = req.body;
        
        if (!code) {
            return res.status(400).json({
                success: false,
                error: 'Authorization code not provided'
            });
        }
        
        // Exchange authorization code for access token
        const tokenResponse = await axios.post('https://oauth2.googleapis.com/token', {
            code,
            client_id: GOOGLE_CLIENT_ID,
            client_secret: GOOGLE_CLIENT_SECRET,
            redirect_uri: redirectUri || `${req.protocol}://${req.get('host')}/auth/google/callback`,
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
                googleId: googleUser.id,
                credits: 100 // Initial credits for new users
            });
            await user.save();
            console.log('✅ New Google OAuth user created:', user.email);
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
        
        // Return token and user data
        res.json({
            success: true,
            token: token,
            user: {
                id: user._id,
                firstName: user.firstName,
                lastName: user.lastName,
                email: user.email,
                credits: user.credits || 0
            }
        });
        
    } catch (error) {
        console.error('Google OAuth Error:', error.message);
        res.status(400).json({
            success: false,
            error: error.response?.data?.error_description || 'Authentication failed. Please try again.'
        });
    }
});

// ===== NEW FEATURES =====

// Get total users count
app.get('/api/stats/users', async (req, res) => {
    try {
        const usersData = await readUsers();
        res.json({
            success: true,
            totalUsers: usersData.users.length
        });
    } catch (error) {
        console.error('Error getting user stats:', error);
        res.status(500).json({ success: false, message: 'Error fetching stats' });
    }
});

// Upload profile picture
app.post('/api/profile/upload-picture', upload.single('profilePicture'), async (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({ success: false, message: 'No file uploaded' });
        }

        const token = req.headers.authorization?.split(' ')[1];
        if (!token) {
            return res.status(401).json({ success: false, message: 'No token provided' });
        }

        const decoded = jwt.verify(token, process.env.JWT_SECRET || 'fallback_secret');
        const usersData = await readUsers();
        const userIndex = usersData.users.findIndex(u => u.email === decoded.email);

        if (userIndex === -1) {
            return res.status(404).json({ success: false, message: 'User not found' });
        }

        // Delete old profile picture if exists
        if (usersData.users[userIndex].profilePicture) {
            const oldPicPath = path.join(__dirname, usersData.users[userIndex].profilePicture);
            try {
                await fs.unlink(oldPicPath);
            } catch (err) {
                console.log('Old profile picture not found or already deleted');
            }
        }

        // Update user with new profile picture path
        usersData.users[userIndex].profilePicture = `/uploads/profile-pics/${req.file.filename}`;
        await writeUsers(usersData);

        res.json({
            success: true,
            message: 'Profile picture updated',
            profilePicture: usersData.users[userIndex].profilePicture
        });
    } catch (error) {
        console.error('Error uploading profile picture:', error);
        res.status(500).json({ success: false, message: 'Error uploading picture' });
    }
});

// Owner panel key validation
app.post('/api/owner/validate-key', authLimiter, async (req, res) => {
    try {
        const { key, username } = req.body;

        if (!key) {
            return res.status(400).json({ success: false, message: 'Key is required' });
        }

        // Make request to external validation API
        const response = await axios.post('https://sonugamingop.tech/connect', {
            key: key,
            username: username || 'LegendShop',
            source: 'web'
        }, {
            headers: {
                'Content-Type': 'application/json'
            },
            timeout: 10000
        });

        if (response.data && response.data.success) {
            res.json({
                success: true,
                data: {
                    slot: response.data.slot || 'N/A',
                    status: response.data.status || 'Active',
                    expiry: response.data.expiry || response.data.exp || 'N/A',
                    join: response.data.join || response.data.telegram || 'N/A'
                }
            });
        } else {
            res.status(401).json({
                success: false,
                message: response.data?.message || 'Invalid key'
            });
        }
    } catch (error) {
        console.error('Owner key validation error:', error.message);
        
        if (error.code === 'ECONNREFUSED' || error.code === 'ETIMEDOUT') {
            return res.status(503).json({
                success: false,
                message: 'Validation service unavailable. Please try again later.'
            });
        }

        res.status(500).json({
            success: false,
            message: 'Error validating key'
        });
    }
});

// Get user profile
app.get('/api/profile', async (req, res) => {
    try {
        const token = req.headers.authorization?.split(' ')[1];
        if (!token) {
            return res.status(401).json({ success: false, message: 'No token provided' });
        }

        const decoded = jwt.verify(token, process.env.JWT_SECRET || 'fallback_secret');
        const usersData = await readUsers();
        const user = usersData.users.find(u => u.email === decoded.email);

        if (!user) {
            return res.status(404).json({ success: false, message: 'User not found' });
        }

        res.json({
            success: true,
            user: {
                firstName: user.firstName,
                lastName: user.lastName,
                email: user.email,
                profilePicture: user.profilePicture || null,
                createdAt: user.createdAt
            }
        });
    } catch (error) {
        console.error('Error getting profile:', error);
        res.status(500).json({ success: false, message: 'Error fetching profile' });
    }
});

// ===== CREDIT & REDEEM CODE ROUTES =====

// Get user credits
app.get('/api/user-credits', apiLimiter, authenticateToken, requireMongoConnection, async (req, res) => {
    try {
        const user = await User.findById(req.userId).select('credits');
        if (!user) {
            return res.status(404).json({
                success: false,
                message: 'User not found'
            });
        }

        res.json({
            success: true,
            credits: user.credits || 0
        });
    } catch (error) {
        console.error('Error fetching credits:', error);
        res.status(500).json({
            success: false,
            message: 'Error fetching credits'
        });
    }
});

// Redeem code
app.post('/api/redeem-code', redeemLimiter, authenticateToken, requireMongoConnection, [
    body('code').trim().notEmpty().withMessage('Redeem code is required')
        .matches(/^[A-Z0-9-]+$/).withMessage('Invalid code format')
], async (req, res) => {
    try {
        // Validate request
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            return res.status(400).json({ 
                success: false, 
                message: errors.array()[0].msg
            });
        }

        const { code } = req.body;

        // Find the redeem code
        const redeemCode = await RedeemCode.findOne({ 
            code: code.toUpperCase() 
        });

        if (!redeemCode) {
            return res.status(404).json({
                success: false,
                message: 'Invalid redeem code'
            });
        }

        // Check if code is active
        if (!redeemCode.isActive) {
            return res.status(400).json({
                success: false,
                message: 'This code has already been used'
            });
        }

        // Check if code has expired
        if (redeemCode.expiresAt && new Date() > redeemCode.expiresAt) {
            return res.status(400).json({
                success: false,
                message: 'This code has expired'
            });
        }

        // Get user
        const user = await User.findById(req.userId);
        if (!user) {
            return res.status(404).json({
                success: false,
                message: 'User not found'
            });
        }

        // Add credits to user
        user.credits = (user.credits || 0) + redeemCode.credits;
        await user.save();

        // Mark code as used
        redeemCode.isActive = false;
        redeemCode.usedBy = user._id;
        redeemCode.usedAt = new Date();
        await redeemCode.save();

        // Create transaction record
        const transaction = new CreditTransaction({
            userId: user._id,
            amount: redeemCode.credits,
            type: 'redeem',
            description: `Redeemed code: ${code}`,
            redeemCode: code
        });
        await transaction.save();

        res.json({
            success: true,
            message: 'Code redeemed successfully!',
            credits: redeemCode.credits,
            newBalance: user.credits
        });

    } catch (error) {
        console.error('Error redeeming code:', error);
        res.status(500).json({
            success: false,
            message: 'Error redeeming code'
        });
    }
});

// Get credit transactions
app.get('/api/credit-transactions', apiLimiter, authenticateToken, requireMongoConnection, async (req, res) => {
    try {
        const transactions = await CreditTransaction.find({ userId: req.userId })
            .sort({ createdAt: -1 })
            .limit(50);

        res.json({
            success: true,
            transactions
        });
    } catch (error) {
        console.error('Error fetching transactions:', error);
        res.status(500).json({
            success: false,
            message: 'Error fetching transactions'
        });
    }
});

// ===== ADMIN ROUTES =====

// Generate redeem code (Admin only)
app.post('/api/admin/generate-code', authLimiter, authenticateToken, requireMongoConnection, requireAdmin, [
    body('credits').isInt({ min: 1, max: 10000 }).withMessage('Credits must be between 1 and 10000'),
    body('expiresInDays').optional().isInt({ min: 1, max: 365 }).withMessage('Expiration days must be between 1 and 365')
], async (req, res) => {
    try {
        // Validate request
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            return res.status(400).json({ 
                success: false, 
                message: errors.array()[0].msg
            });
        }

        const { credits, expiresInDays } = req.body;

        // Generate cryptographically secure random code
        const generateSecureCode = () => {
            const bytes = crypto.randomBytes(8);
            const part1 = bytes.slice(0, 4).toString('hex').toUpperCase();
            const part2 = bytes.slice(4, 8).toString('hex').toUpperCase();
            return `${part1}-${part2}`;
        };
        const code = generateSecureCode();

        // Calculate expiration date if provided
        let expiresAt = null;
        if (expiresInDays && expiresInDays > 0) {
            expiresAt = new Date();
            expiresAt.setDate(expiresAt.getDate() + expiresInDays);
        }

        // Create redeem code
        const redeemCode = new RedeemCode({
            code,
            credits,
            createdBy: req.userId,
            expiresAt
        });

        await redeemCode.save();

        res.json({
            success: true,
            message: 'Redeem code generated successfully',
            code: {
                code: redeemCode.code,
                credits: redeemCode.credits,
                expiresAt: redeemCode.expiresAt,
                createdAt: redeemCode.createdAt
            }
        });

    } catch (error) {
        console.error('Error generating code:', error);
        res.status(500).json({
            success: false,
            message: 'Error generating code'
        });
    }
});

// Get all redeem codes (Admin only)
app.get('/api/admin/codes', apiLimiter, authenticateToken, requireMongoConnection, requireAdmin, async (req, res) => {
    try {
        const codes = await RedeemCode.find()
            .populate('createdBy', 'firstName lastName email')
            .populate('usedBy', 'firstName lastName email')
            .sort({ createdAt: -1 });

        res.json({
            success: true,
            codes
        });
    } catch (error) {
        console.error('Error fetching codes:', error);
        res.status(500).json({
            success: false,
            message: 'Error fetching codes'
        });
    }
});

// Delete redeem code (Admin only)
app.delete('/api/admin/codes/:id', authLimiter, authenticateToken, requireMongoConnection, requireAdmin, async (req, res) => {
    try {
        const { id } = req.params;

        const code = await RedeemCode.findByIdAndDelete(id);
        if (!code) {
            return res.status(404).json({
                success: false,
                message: 'Code not found'
            });
        }

        res.json({
            success: true,
            message: 'Code deleted successfully'
        });
    } catch (error) {
        console.error('Error deleting code:', error);
        res.status(500).json({
            success: false,
            message: 'Error deleting code'
        });
    }
});

// Deduct credits for card check
app.post('/api/deduct-credit', apiLimiter, authenticateToken, requireMongoConnection, async (req, res) => {
    try {
        const { amount, description } = req.body;
        const deductAmount = amount || 1; // Default 1 credit per check

        const user = await User.findById(req.userId);
        if (!user) {
            return res.status(404).json({
                success: false,
                message: 'User not found'
            });
        }

        // Check if user has enough credits
        if ((user.credits || 0) < deductAmount) {
            return res.status(400).json({
                success: false,
                message: 'Insufficient credits',
                currentBalance: user.credits || 0,
                required: deductAmount
            });
        }

        // Deduct credits
        user.credits = (user.credits || 0) - deductAmount;
        await user.save();

        // Create transaction record
        const transaction = new CreditTransaction({
            userId: user._id,
            amount: -deductAmount, // Negative for deduction
            type: 'usage',
            description: description || 'Card check operation'
        });
        await transaction.save();

        res.json({
            success: true,
            message: 'Credit deducted successfully',
            deducted: deductAmount,
            newBalance: user.credits
        });
    } catch (error) {
        console.error('Error deducting credits:', error);
        res.status(500).json({
            success: false,
            message: 'Error deducting credits'
        });
    }
});

// Get user info including role and limits
app.get('/api/user-info', apiLimiter, authenticateToken, requireMongoConnection, async (req, res) => {
    try {
        const user = await User.findById(req.userId).select('firstName lastName email credits role checkLimit');
        if (!user) {
            return res.status(404).json({
                success: false,
                message: 'User not found'
            });
        }

        // Get owner proxy if available (check settings collection or environment)
        const ownerProxy = process.env.OWNER_PROXY || null;

        res.json({
            success: true,
            user: {
                firstName: user.firstName,
                lastName: user.lastName,
                email: user.email,
                credits: user.credits || 0,
                role: user.role || 'free',
                checkLimit: user.checkLimit || 1000
            },
            ownerProxy: ownerProxy
        });
    } catch (error) {
        console.error('Error fetching user info:', error);
        res.status(500).json({
            success: false,
            message: 'Error fetching user info'
        });
    }
});

// Save charged card to vault
app.post('/api/vault/save-charged', apiLimiter, authenticateToken, requireMongoConnection, async (req, res) => {
    try {
        const { cardData, site, timestamp } = req.body;

        // Here you would save to a Vault collection
        // For now, just acknowledge
        console.log('Charged card saved:', { userId: req.userId, site, timestamp });

        res.json({
            success: true,
            message: 'Charged card saved to vault'
        });
    } catch (error) {
        console.error('Error saving charged card:', error);
        res.status(500).json({
            success: false,
            message: 'Error saving to vault'
        });
    }
});

// Add credits to user (Admin only)
app.post('/api/admin/add-credits', authLimiter, authenticateToken, requireMongoConnection, requireAdmin, async (req, res) => {
    try {
        const { userId, credits, description } = req.body;

        if (!userId || !credits || credits <= 0) {
            return res.status(400).json({
                success: false,
                message: 'Invalid request data'
            });
        }

        const user = await User.findById(userId);
        if (!user) {
            return res.status(404).json({
                success: false,
                message: 'User not found'
            });
        }

        user.credits = (user.credits || 0) + credits;
        await user.save();

        // Create transaction record
        const transaction = new CreditTransaction({
            userId: user._id,
            amount: credits,
            type: 'admin',
            description: description || 'Admin credit addition'
        });
        await transaction.save();

        res.json({
            success: true,
            message: 'Credits added successfully',
            newBalance: user.credits
        });
    } catch (error) {
        console.error('Error adding credits:', error);
        res.status(500).json({
            success: false,
            message: 'Error adding credits'
        });
    }
});

// Serve uploaded files
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// Start Server
const PORT = process.env.PORT || 3000;

// Initialize storage before starting server
initializeStorage().then(() => {
    app.listen(PORT, () => {
        console.log(`🚀 Legend Shop Server running on http://localhost:${PORT}`);
        console.log(`📝 API Endpoints:`);
        console.log(`   - POST http://localhost:${PORT}/api/signup`);
        console.log(`   - POST http://localhost:${PORT}/api/login`);
        console.log(`   - GET  http://localhost:${PORT}/api/users`);
        console.log(`   - GET  http://localhost:${PORT}/api/stats/users`);
        console.log(`   - POST http://localhost:${PORT}/api/profile/upload-picture`);
        console.log(`   - POST http://localhost:${PORT}/api/owner/validate-key`);
        console.log(`   - GET  http://localhost:${PORT}/api/user-credits`);
        console.log(`   - POST http://localhost:${PORT}/api/redeem-code`);
        console.log(`   - GET  http://localhost:${PORT}/api/credit-transactions`);
        console.log(`   - POST http://localhost:${PORT}/api/admin/generate-code (Admin)`);
        console.log(`   - GET  http://localhost:${PORT}/api/admin/codes (Admin)`);
    });
});
