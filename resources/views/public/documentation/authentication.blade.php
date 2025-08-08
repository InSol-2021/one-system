@extends('public.layouts.app')

@section('title', 'Authentication Guide - CAS SSO System')

@section('content')
<div class="bg-gradient-to-br from-blue-900 via-blue-800 to-purple-900 text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">{{ $authGuide['title'] }}</h1>
            <p class="text-xl text-blue-100">{{ $authGuide['description'] }}</p>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Table of Contents -->
    <div class="bg-gray-50 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Table of Contents</h2>
        <ul class="space-y-2">
            @foreach($authGuide['sections'] as $key => $section)
                <li><a href="#{{ $key }}" class="text-blue-600 hover:text-blue-800">{{ $section }}</a></li>
            @endforeach
        </ul>
    </div>

    <!-- Authentication Overview -->
    <section id="overview" class="mb-12">
        <h2 class="text-3xl font-bold mb-6">Authentication Overview</h2>
        
        <div class="prose max-w-none">
            <p class="text-lg text-gray-700 mb-6">
                The CAS (Central Authentication Service) system provides enterprise-grade authentication 
                with support for multiple client applications, secure token management, and comprehensive 
                audit logging.
            </p>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-blue-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3">Key Features</h3>
                    <ul class="space-y-2 text-blue-800">
                        <li>• JWT-based token authentication</li>
                        <li>• Client credential management</li>
                        <li>• IP whitelist security</li>
                        <li>• HMAC-SHA256 signature validation</li>
                        <li>• Comprehensive audit logging</li>
                    </ul>
                </div>
                
                <div class="bg-green-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-green-900 mb-3">Security Benefits</h3>
                    <ul class="space-y-2 text-green-800">
                        <li>• Single sign-on across applications</li>
                        <li>• Centralized user management</li>
                        <li>• Anti-replay protection</li>
                        <li>• Role-based access control</li>
                        <li>• Secure credential handling</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Authentication Flows -->
    <section id="flows" class="mb-12">
        <h2 class="text-3xl font-bold mb-6">Authentication Flows</h2>
        
        <div class="space-y-8">
            <!-- Traditional SSO Flow -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4">1. Traditional SSO Flow</h3>
                <p class="text-gray-700 mb-4">
                    Client systems authenticate directly using client credentials and username.
                </p>
                
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <h4 class="font-semibold mb-2">Flow Steps:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-sm">
                        <li>Client system sends POST request to <code>/api/sso/token</code></li>
                        <li>Include client_id, client_secret, and username in request</li>
                        <li>CAS validates credentials and IP whitelist</li>
                        <li>JWT token returned for authenticated sessions</li>
                        <li>Client uses token for subsequent API calls</li>
                    </ol>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold mb-2">Example Request:</h4>
                    <pre class="text-sm overflow-x-auto"><code>POST /api/sso/token
Content-Type: application/json

{
  "client_id": "your_client_id",
  "client_secret": "your_client_secret",
  "username": "user@example.com"
}</code></pre>
                </div>
            </div>

            <!-- Dashboard SSO Flow -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4">2. Dashboard SSO Flow</h3>
                <p class="text-gray-700 mb-4">
                    Users link their accounts to client systems through the user dashboard.
                </p>
                
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <h4 class="font-semibold mb-2">Flow Steps:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-sm">
                        <li>User logs into CAS user dashboard</li>
                        <li>User links username to available client systems</li>
                        <li>One-click SSO login generates secure tokens</li>
                        <li>Automatic redirect to client application</li>
                        <li>User authenticated without entering credentials</li>
                    </ol>
                </div>

                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold mb-2">Benefits:</h4>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <li>No password storage required</li>
                        <li>Enhanced user experience</li>
                        <li>Centralized account management</li>
                        <li>Reduced security risks</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- JWT Token Management -->
    <section id="tokens" class="mb-12">
        <h2 class="text-3xl font-bold mb-6">JWT Token Management</h2>
        
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4">Token Structure</h3>
                <p class="text-gray-700 mb-4">
                    CAS generates JWT tokens with enhanced payloads containing user information, 
                    client system mapping, and security metadata.
                </p>

                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <h4 class="font-semibold mb-2">Token Payload:</h4>
                    <pre class="text-sm overflow-x-auto"><code>{
  "user_id": "123",
  "username": "user@example.com",
  "client_id": "client_abc123",
  "client_name": "Customer Portal",
  "roles": ["user", "customer"],
  "ip_address": "192.168.1.100",
  "iat": 1640995200,
  "exp": 1641081600,
  "iss": "cas-system",
  "aud": "client_applications"
}</code></pre>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold mb-2">Token Validation</h4>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            <li>Signature verification</li>
                            <li>Expiration checking</li>
                            <li>Issuer validation</li>
                            <li>Audience verification</li>
                        </ul>
                    </div>
                    
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h4 class="font-semibold mb-2">Security Features</h4>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            <li>HMAC-SHA256 signing</li>
                            <li>IP address binding</li>
                            <li>Role-based claims</li>
                            <li>Audit trail tracking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Features -->
    <section id="security" class="mb-12">
        <h2 class="text-3xl font-bold mb-6">Security Features</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">IP Whitelist Protection</h3>
                <p class="text-gray-700 mb-4">
                    Restrict access to specific IP addresses or ranges for enhanced security.
                </p>
                <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                    <li>Single IP address filtering</li>
                    <li>CIDR range support</li>
                    <li>Multiple IP configuration</li>
                    <li>Real-time validation</li>
                </ul>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">HMAC Signature Validation</h3>
                <p class="text-gray-700 mb-4">
                    Cryptographic signatures prevent replay attacks and ensure data integrity.
                </p>
                <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                    <li>SHA-256 hash algorithm</li>
                    <li>Timestamp validation</li>
                    <li>Anti-replay protection</li>
                    <li>Request body verification</li>
                </ul>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Audit Logging</h3>
                <p class="text-gray-700 mb-4">
                    Comprehensive logging of all authentication events and system activities.
                </p>
                <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                    <li>Login/logout tracking</li>
                    <li>Token generation logs</li>
                    <li>Failed attempt monitoring</li>
                    <li>IP address recording</li>
                </ul>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Credential Security</h3>
                <p class="text-gray-700 mb-4">
                    Secure handling of client credentials with one-time display and regeneration.
                </p>
                <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                    <li>One-time credential display</li>
                    <li>Automatic credential masking</li>
                    <li>Secure regeneration</li>
                    <li>bcrypt/scrypt hashing</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Implementation Examples -->
    <section id="examples" class="mb-12">
        <h2 class="text-3xl font-bold mb-6">Implementation Examples</h2>
        
        <div class="space-y-8">
            <!-- PHP Laravel Example -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4">PHP Laravel Integration</h3>
                <p class="text-gray-700 mb-4">
                    Complete Laravel middleware for CAS authentication.
                </p>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-sm overflow-x-auto"><code>// Install the CAS client package
composer require one-system/client

// Configure in config/cas.php
return [
    'cas_url' => env('CAS_URL', 'https://cas.example.com'),
    'client_id' => env('CAS_CLIENT_ID'),
    'client_secret' => env('CAS_CLIENT_SECRET'),
];

// Use middleware in routes
Route::middleware(['cas.auth'])->group(function () {
    Route::get('/dashboard', 'DashboardController@index');
});

// Access user information
$user = Auth::user(); // CAS authenticated user</code></pre>
                </div>
            </div>

            <!-- Node.js Example -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4">Node.js Express Integration</h3>
                <p class="text-gray-700 mb-4">
                    Express.js middleware for JWT token validation.
                </p>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-sm overflow-x-auto"><code>const jwt = require('jsonwebtoken');
const axios = require('axios');

// CAS authentication middleware
const casAuth = async (req, res, next) => {
  const token = req.headers.authorization?.replace('Bearer ', '');
  
  if (!token) {
    return res.status(401).json({ error: 'No token provided' });
  }

  try {
    // Validate token with CAS
    const response = await axios.post('https://cas.example.com/api/sso/validate', {
      token: token
    });

    req.user = response.data.user;
    next();
  } catch (error) {
    res.status(401).json({ error: 'Invalid token' });
  }
};

// Use middleware
app.use('/protected', casAuth);
app.get('/protected/dashboard', (req, res) => {
  res.json({ user: req.user });
});</code></pre>
                </div>
            </div>

            <!-- JavaScript Frontend Example -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-4">JavaScript Frontend Integration</h3>
                <p class="text-gray-700 mb-4">
                    Frontend JavaScript for handling CAS authentication.
                </p>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-sm overflow-x-auto"><code>// CAS authentication class
class CASAuth {
  constructor(casUrl, clientId) {
    this.casUrl = casUrl;
    this.clientId = clientId;
    this.token = localStorage.getItem('cas_token');
  }

  async login(username, password) {
    const response = await fetch(`${this.casUrl}/api/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
    });

    const data = await response.json();
    this.token = data.token;
    localStorage.setItem('cas_token', this.token);
    return data;
  }

  async makeAuthenticatedRequest(url, options = {}) {
    return fetch(url, {
      ...options,
      headers: {
        ...options.headers,
        'Authorization': `Bearer ${this.token}`
      }
    });
  }
}

// Usage
const cas = new CASAuth('https://cas.example.com', 'your_client_id');
cas.login('user@example.com', 'password');</code></pre>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links -->
    <div class="bg-blue-50 rounded-lg p-6 mt-8">
        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <a href="{{ route('docs.api.overview') }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <h4 class="font-semibold text-blue-900">API Reference</h4>
                <p class="text-sm text-gray-600">Complete API documentation</p>
            </a>
            <a href="{{ route('docs.examples') }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <h4 class="font-semibold text-blue-900">Code Examples</h4>
                <p class="text-sm text-gray-600">Implementation examples</p>
            </a>
            <a href="{{ route('docs') }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <h4 class="font-semibold text-blue-900">Documentation Home</h4>
                <p class="text-sm text-gray-600">Return to main docs</p>
            </a>
        </div>
    </div>
</div>
@endsection