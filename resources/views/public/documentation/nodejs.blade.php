@extends('public.documentation.layout')

@section('title', 'Node.js/Express CAS SSO Integration Guide - Enhanced Architecture')  
@section('description', 'Complete guide for integrating Node.js/Express applications with our enhanced CAS Single Sign-On system featuring Livewire components and PostgreSQL multi-schema design.')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                <i class="fab fa-node-js text-green-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $nodejsGuide['title'] }}</h1>
                <p class="text-gray-600 mt-1">{{ $nodejsGuide['description'] }}</p>
            </div>
        </div>
        
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        <strong>Updated Architecture:</strong> Our CAS system now features modular Admin/User/Public separation with enhanced JWT authentication and PostgreSQL multi-schema design.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <span><i class="fas fa-clock mr-1"></i>Setup time: 10 minutes</span>
            <span><i class="fas fa-code mr-1"></i>Difficulty: Medium</span>
            <span><i class="fas fa-tag mr-1"></i>Node.js 14+</span>
        </div>
    </div>

    <!-- Installation -->
    <section id="installation" class="mb-12">
        <h2 class="text-2xl font-bold mb-4">1. Installation</h2>
        
        <div class="code-block mb-6">
            <pre class="language-bash"><code>npm install express cors axios jsonwebtoken crypto</code></pre>
        </div>

        <h3 class="text-xl font-semibold mb-3">Project Structure</h3>
        <div class="code-block mb-6">
            <pre class="language-text"><code>your-node-app/
├── config/
│   └── cas.js           # CAS configuration
├── middleware/
│   └── casAuth.js       # CAS authentication middleware
├── services/
│   └── casClient.js     # CAS client service
├── routes/
│   ├── auth.js          # Authentication routes
│   └── protected.js     # Protected routes
└── app.js               # Main application</code></pre>
        </div>
    </section>

    <!-- Configuration -->
    <section id="configuration" class="mb-12">
        <h2 class="text-2xl font-bold mb-4">2. Configuration</h2>
        
        <h3 class="text-xl font-semibold mb-3">Environment Variables</h3>
        <div class="code-block mb-6">
            <pre class="language-env"><code># CAS Configuration - Enhanced Architecture
CAS_SERVER_URL=https://your-cas-server.com
CAS_CLIENT_ID=your-client-id
CAS_CLIENT_SECRET=your-client-secret
CAS_SIGNATURE_SECRET=your-signature-secret
CAS_CALLBACK_URL=http://your-app.com/auth/cas/callback

# Database Configuration (if needed)
CAS_DB_HOST=127.0.0.1
CAS_DB_PORT=5432
CAS_DB_NAME=cas_system
CAS_DB_USER=cas_user
CAS_DB_PASSWORD=secure_password

# New Route Configuration
CAS_SSO_TOKEN_ENDPOINT=/api/sso/token
CAS_SSO_VALIDATE_ENDPOINT=/api/sso/validate
CAS_USER_DASHBOARD_URL=/user/dashboard</code></pre>
        </div>

        <h3 class="text-xl font-semibold mb-3">Configuration File</h3>
        <p class="text-gray-700 mb-4">Create <code class="bg-gray-100 px-2 py-1 rounded">config/cas.js</code>:</p>
        
        <div class="code-block mb-6">
            <pre class="language-javascript"><code>module.exports = {
    // Enhanced CAS Server Configuration
    serverUrl: process.env.CAS_SERVER_URL || 'https://your-cas-server.com',
    clientId: process.env.CAS_CLIENT_ID,
    clientSecret: process.env.CAS_CLIENT_SECRET,
    signatureSecret: process.env.CAS_SIGNATURE_SECRET,
    callbackUrl: process.env.CAS_CALLBACK_URL,
    
    // New Route Endpoints - Updated Architecture
    endpoints: {
        ssoToken: '/api/sso/token',           // Enhanced client credentials
        ssoValidate: '/api/sso/validate',     // Token validation
        callback: '/auth/sso/callback',       // SSO callback
        userDashboard: '/user/dashboard',     // User dashboard
        logout: '/auth/logout'                // Logout endpoint
    },
    
    // Enhanced Security Settings
    security: {
        tokenExpiry: 3600,                    // 1 hour
        hmacAlgorithm: 'sha256',              // HMAC-SHA256
        ipWhitelistEnabled: true,             // IP whitelist support
        auditLogging: true,                   // Comprehensive logging
        rateLimiting: true                    // Rate limiting
    },
    
    // PostgreSQL Schema Configuration
    database: {
        schemas: {
            admin: 'cas_admin',
            user: 'cas_user',
            public: 'cas_public', 
            audit: 'cas_audit'
        }
    }
};</code></pre>
        </div>
    </section>

    <!-- CAS Client Service -->
    <section id="service" class="mb-12">
        <h2 class="text-2xl font-bold mb-4">3. CAS Client Service</h2>
        
        <p class="text-gray-700 mb-4">Create <code class="bg-gray-100 px-2 py-1 rounded">services/casClient.js</code>:</p>
        
        <div class="code-block mb-6">
            <pre class="language-javascript"><code>const axios = require('axios');
const crypto = require('crypto');
const jwt = require('jsonwebtoken');
const config = require('../config/cas');

class CasClient {
    constructor() {
        this.baseURL = config.serverUrl;
        this.clientId = config.clientId;
        this.clientSecret = config.clientSecret;
        this.signatureSecret = config.signatureSecret;
    }

    // Enhanced Token Request with HMAC Signature
    async requestToken(username) {
        const timestamp = Date.now();
        const requestData = {
            client_id: this.clientId,
            client_secret: this.clientSecret,
            username: username,
            timestamp: timestamp
        };

        // Generate HMAC-SHA256 signature
        const signature = this.generateSignature(requestData);
        requestData.signature = signature;

        try {
            const response = await axios.post(
                `${this.baseURL}${config.endpoints.ssoToken}`,
                requestData,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CAS-Client-ID': this.clientId
                    }
                }
            );

            return response.data;
        } catch (error) {
            console.error('CAS Token Request Error:', error.response?.data || error.message);
            throw new Error('Failed to obtain CAS token');
        }
    }

    // Enhanced Token Validation
    async validateToken(token) {
        const requestData = {
            token: token,
            timestamp: Date.now()
        };

        const signature = this.generateSignature(requestData);
        requestData.signature = signature;

        try {
            const response = await axios.post(
                `${this.baseURL}${config.endpoints.ssoValidate}`,
                requestData,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CAS-Client-ID': this.clientId
                    }
                }
            );

            return response.data;
        } catch (error) {
            console.error('CAS Token Validation Error:', error.response?.data || error.message);
            return { valid: false, error: 'Token validation failed' };
        }
    }

    // HMAC-SHA256 Signature Generation
    generateSignature(data) {
        const sortedKeys = Object.keys(data).sort();
        const queryString = sortedKeys
            .map(key => `${key}=${encodeURIComponent(data[key])}`)
            .join('&');
        
        return crypto
            .createHmac('sha256', this.signatureSecret)
            .update(queryString)
            .digest('hex');
    }

    // Generate SSO Login URL
    getLoginUrl(returnUrl) {
        const params = new URLSearchParams({
            client_id: this.clientId,
            redirect_uri: config.callbackUrl,
            return_url: returnUrl || '/'
        });

        return `${this.baseURL}/auth/sso?${params.toString()}`;
    }

    // Process SSO Callback
    async processCallback(callbackData) {
        try {
            const validationResult = await this.validateToken(callbackData.token);
            
            if (validationResult.valid) {
                return {
                    success: true,
                    user: validationResult.user,
                    token: callbackData.token,
                    returnUrl: callbackData.return_url || '/'
                };
            } else {
                return {
                    success: false,
                    error: 'Invalid token'
                };
            }
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }
}

module.exports = new CasClient();</code></pre>
        </div>
    </section>

    <!-- Middleware -->
    <section id="middleware" class="mb-12">
        <h2 class="text-2xl font-bold mb-4">4. Authentication Middleware</h2>
        
        <p class="text-gray-700 mb-4">Create <code class="bg-gray-100 px-2 py-1 rounded">middleware/casAuth.js</code>:</p>
        
        <div class="code-block mb-6">
            <pre class="language-javascript"><code>const casClient = require('../services/casClient');

// Enhanced CAS Authentication Middleware
const casAuth = async (req, res, next) => {
    try {
        // Check for existing session
        if (req.session && req.session.cas_user && req.session.cas_token) {
            // Validate existing token
            const validation = await casClient.validateToken(req.session.cas_token);
            
            if (validation.valid) {
                req.user = req.session.cas_user;
                req.casToken = req.session.cas_token;
                return next();
            } else {
                // Clear invalid session
                req.session.cas_user = null;
                req.session.cas_token = null;
            }
        }

        // Redirect to CAS login
        const returnUrl = req.originalUrl;
        const loginUrl = casClient.getLoginUrl(returnUrl);
        
        // Store return URL in session
        req.session.cas_return_url = returnUrl;
        
        return res.redirect(loginUrl);
        
    } catch (error) {
        console.error('CAS Auth Middleware Error:', error);
        return res.status(500).json({ 
            error: 'Authentication error',
            message: 'Please try again later'
        });
    }
};

// Optional: Role-based access control
const casRole = (requiredRoles = []) => {
    return (req, res, next) => {
        if (!req.user || !req.user.roles) {
            return res.status(403).json({ 
                error: 'Access denied',
                message: 'Insufficient permissions'
            });
        }

        const userRoles = Array.isArray(req.user.roles) ? req.user.roles : [req.user.roles];
        const hasRole = requiredRoles.some(role => userRoles.includes(role));

        if (!hasRole) {
            return res.status(403).json({ 
                error: 'Access denied',
                message: 'Required role not found'
            });
        }

        next();
    };
};

module.exports = { casAuth, casRole };</code></pre>
        </div>
    </section>

    <!-- Routes Implementation -->
    <section id="routes" class="mb-12">
        <h2 class="text-2xl font-bold mb-4">5. Routes Implementation</h2>
        
        <h3 class="text-xl font-semibold mb-3">Authentication Routes</h3>
        <p class="text-gray-700 mb-4">Create <code class="bg-gray-100 px-2 py-1 rounded">routes/auth.js</code>:</p>
        
        <div class="code-block mb-6">
            <pre class="language-javascript"><code>const express = require('express');
const router = express.Router();
const casClient = require('../services/casClient');

// Enhanced SSO Callback Handler
router.get('/cas/callback', async (req, res) => {
    try {
        const { token, return_url } = req.query;
        
        if (!token) {
            return res.status(400).json({ 
                error: 'Missing token parameter' 
            });
        }

        // Process callback with enhanced validation
        const result = await casClient.processCallback({
            token: token,
            return_url: return_url
        });

        if (result.success) {
            // Store user session
            req.session.cas_user = result.user;
            req.session.cas_token = result.token;
            
            // Redirect to return URL or dashboard
            const redirectUrl = result.returnUrl || '/user/dashboard';
            return res.redirect(redirectUrl);
        } else {
            return res.status(401).json({ 
                error: 'Authentication failed',
                message: result.error
            });
        }
        
    } catch (error) {
        console.error('SSO Callback Error:', error);
        return res.status(500).json({ 
            error: 'Callback processing failed',
            message: 'Please try again later'
        });
    }
});

// Enhanced Logout Handler
router.post('/logout', (req, res) => {
    // Clear CAS session
    req.session.cas_user = null;
    req.session.cas_token = null;
    
    // Destroy entire session
    req.session.destroy((err) => {
        if (err) {
            console.error('Session destruction error:', err);
        }
        
        res.clearCookie('connect.sid'); // Clear session cookie
        return res.json({ 
            success: true,
            message: 'Logged out successfully',
            redirect: '/auth/login'
        });
    });
});

// User Info Endpoint
router.get('/user', (req, res) => {
    if (req.session && req.session.cas_user) {
        return res.json({
            success: true,
            user: req.session.cas_user,
            authenticated: true
        });
    } else {
        return res.status(401).json({
            success: false,
            authenticated: false,
            message: 'Not authenticated'
        });
    }
});

module.exports = router;</code></pre>
        </div>

        <h3 class="text-xl font-semibold mb-3">Protected Routes</h3>
        <p class="text-gray-700 mb-4">Create <code class="bg-gray-100 px-2 py-1 rounded">routes/protected.js</code>:</p>
        
        <div class="code-block mb-6">
            <pre class="language-javascript"><code>const express = require('express');
const router = express.Router();
const { casAuth, casRole } = require('../middleware/casAuth');

// Apply CAS authentication to all protected routes
router.use(casAuth);

// User Dashboard - Enhanced with new architecture
router.get('/user/dashboard', (req, res) => {
    res.json({
        success: true,
        message: 'Welcome to your dashboard',
        user: req.user,
        features: [
            'Client System Linking',
            'One-Click SSO Login', 
            'Profile Management',
            'System Access Control'
        ],
        architecture: {
            separation: 'Admin/User/Public',
            database: 'PostgreSQL Multi-Schema',
            performance: 'Livewire Optimized'
        }
    });
});

// Admin Routes (require admin role)
router.get('/admin/systems', casRole(['admin']), (req, res) => {
    res.json({
        success: true,
        message: 'Admin client systems management',
        user: req.user,
        features: [
            'Client System Creation',
            'IP Whitelist Management',
            'Audit Log Viewing',
            'Security Configuration'
        ]
    });
});

// Protected API Endpoint
router.get('/api/profile', (req, res) => {
    res.json({
        success: true,
        profile: {
            id: req.user.id,
            username: req.user.username,
            email: req.user.email,
            roles: req.user.roles,
            last_login: req.user.last_login,
            client_systems: req.user.client_systems || []
        }
    });
});

module.exports = router;</code></pre>
        </div>
    </section>

    <!-- Main Application -->
    <section id="app" class="mb-12">
        <h2 class="text-2xl font-bold mb-4">6. Main Application Setup</h2>
        
        <p class="text-gray-700 mb-4">Update your <code class="bg-gray-100 px-2 py-1 rounded">app.js</code>:</p>
        
        <div class="code-block mb-6">
            <pre class="language-javascript"><code>const express = require('express');
const session = require('express-session');
const cors = require('cors');
const config = require('./config/cas');

const app = express();

// Enhanced Middleware Configuration
app.use(cors({
    origin: process.env.ALLOWED_ORIGINS?.split(',') || ['http://localhost:3000'],
    credentials: true
}));

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Enhanced Session Configuration
app.use(session({
    secret: process.env.SESSION_SECRET || 'your-session-secret',
    resave: false,
    saveUninitialized: false,
    cookie: {
        secure: process.env.NODE_ENV === 'production',
        httpOnly: true,
        maxAge: config.security.tokenExpiry * 1000
    }
}));

// Route Registration
const authRoutes = require('./routes/auth');
const protectedRoutes = require('./routes/protected');

app.use('/auth', authRoutes);
app.use('/', protectedRoutes);

// Health Check - Updated Architecture Info
app.get('/health', (req, res) => {
    res.json({
        status: 'healthy',
        timestamp: new Date().toISOString(),
        services: {
            cas_client: 'connected',
            session_store: 'active',
            authentication: 'enabled'
        },
        architecture: {
            type: 'CAS Client - Node.js/Express',
            version: '2.0',
            features: [
                'Enhanced JWT Authentication',
                'HMAC-SHA256 Signature Validation',
                'Admin/User/Public Route Separation',
                'PostgreSQL Multi-Schema Support'
            ]
        }
    });
});

// Error Handler
app.use((error, req, res, next) => {
    console.error('Application Error:', error);
    res.status(500).json({
        error: 'Internal server error',
        message: process.env.NODE_ENV === 'development' ? error.message : 'Something went wrong'
    });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`CAS Client Application running on port ${PORT}`);
    console.log(`Architecture: Enhanced Admin/User/Public Separation`);
    console.log(`CAS Server: ${config.serverUrl}`);
});

module.exports = app;</code></pre>
        </div>
    </section>

    <!-- Advanced Usage -->
    <section id="advanced" class="mb-12">
        <h2 class="text-2xl font-bold mb-4">7. Advanced Usage</h2>
        
        <h3 class="text-xl font-semibold mb-3">Custom User Dashboard Integration</h3>
        <div class="code-block mb-6">
            <pre class="language-javascript"><code>// Enhanced dashboard with client system linking
router.get('/user/dashboard', casAuth, async (req, res) => {
    try {
        // Fetch user's linked client systems
        const linkedSystems = await getUserLinkedSystems(req.user.id);
        
        res.render('dashboard', {
            user: req.user,
            linkedSystems: linkedSystems,
            features: {
                ssoLogin: true,
                systemLinking: true,
                profileManagement: true
            },
            architecture: 'Admin/User/Public Separation'
        });
    } catch (error) {
        res.status(500).render('error', { error: error.message });
    }
});</code></pre>
        </div>

        <h3 class="text-xl font-semibold mb-3">Database Integration</h3>
        <div class="code-block mb-6">
            <pre class="language-javascript"><code>// PostgreSQL connection with schema support
const { Pool } = require('pg');

const pool = new Pool({
    host: process.env.CAS_DB_HOST,
    port: process.env.CAS_DB_PORT,
    database: process.env.CAS_DB_NAME,
    user: process.env.CAS_DB_USER,
    password: process.env.CAS_DB_PASSWORD,
    searchPath: ['cas_user', 'cas_public'] // Multi-schema support
});

// Query user data from CAS schemas
async function getUserData(userId) {
    const query = `
        SELECT u.*, ucl.client_system_id, cs.name as system_name
        FROM cas_user.users u
        LEFT JOIN cas_user.user_client_links ucl ON u.id = ucl.user_id
        LEFT JOIN cas_admin.client_systems cs ON ucl.client_system_id = cs.id
        WHERE u.id = $1
    `;
    
    const result = await pool.query(query, [userId]);
    return result.rows;
}</code></pre>
        </div>
    </section>

    <!-- Testing -->
    <section id="testing" class="mb-12">
        <h2 class="text-2xl font-bold mb-4">8. Testing Your Integration</h2>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-green-900 mb-4">Test Checklist</h3>
            <ul class="space-y-2 text-green-800">
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Authentication flow works end-to-end
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Token validation and signature verification
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Protected routes require authentication
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Session management works correctly
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Logout functionality clears session
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Enhanced routes (/user/dashboard) accessible
                </li>
            </ul>
        </div>
    </section>

    <!-- Navigation -->
    <div class="flex justify-between items-center pt-8 border-t border-gray-200">
        <a href="{{ route('docs') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Back to Documentation
        </a>
        <div class="flex space-x-4">
            <a href="{{ route('docs.section', 'java') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                Java Integration →
            </a>
        </div>
    </div>
</div>
@endsection