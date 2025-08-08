@extends('public.documentation.layout')

@section('title', 'CAS SSO API Reference')
@section('description', 'Complete API reference for CAS Single Sign-On authentication system endpoints and responses.')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            <i class="fas fa-shield-alt text-blue-600 mr-3"></i>
            CAS SSO API Reference
        </h1>
        <p class="text-gray-600">Enterprise-grade API documentation for CAS authentication with enhanced security features</p>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                <div class="flex items-center">
                    <i class="fas fa-robot text-green-600 mr-2"></i>
                    <span class="text-sm font-semibold text-green-800">reCAPTCHA v3 Protected</span>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-center">
                    <i class="fas fa-ban text-blue-600 mr-2"></i>
                    <span class="text-sm font-semibold text-blue-800">Account Lockout System</span>
                </div>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                <div class="flex items-center">
                    <i class="fas fa-tachometer-alt text-purple-600 mr-2"></i>
                    <span class="text-sm font-semibold text-purple-800">Rate Limited (5/min)</span>
                </div>
            </div>
        </div>
    </div>

    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">Base URL & Security</h2>
        <div class="code-block mb-6">
            <pre class="language-bash"><code>http://localhost:8000/api  # Laravel CAS Server
http://localhost:5000/api  # Express.js CAS Server</code></pre>
        </div>
        <p class="text-gray-600 mb-4">All API endpoints are protected by multiple security layers:</p>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
            <h4 class="font-semibold text-amber-800 mb-2">
                <i class="fas fa-exclamation-triangle text-amber-600 mr-2"></i>
                Security Requirements
            </h4>
            <ul class="text-amber-700 space-y-1 ml-4">
                <li>• IP Whitelisting: Client IPs must be registered in the system</li>
                <li>• Rate Limiting: Maximum 5 requests per minute per IP</li>
                <li>• HMAC Signature: All requests require valid HMAC-SHA256 signatures</li>
                <li>• Account Lockout: 5 failed attempts = 30-minute lockout</li>
                <li>• reCAPTCHA: Human verification on login forms</li>
            </ul>
        </div>
    </section>

    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">Authentication</h2>

        <h3 class="text-xl font-semibold mb-3">POST /sso/token</h3>
        <p class="text-gray-600 mb-4">Generate an SSO token for authenticated users.</p>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Request Headers:</h4>
            <div class="code-block mb-3">
                <pre class="language-bash"><code>Content-Type: application/json
X-Signature: sha256=HMAC_SHA256_SIGNATURE
X-Timestamp: 1640995200</code></pre>
            </div>

            <h4 class="font-semibold mb-2">Request Body:</h4>
            <div class="code-block">
                <pre class="language-json"><code>{
  "email": "john@example.com",
  "password": "SecureP@ss123!",
  "client_id": "your_client_id",
  "client_secret": "your_client_secret",
  "client_username": "your_client_username",
  "client_password": "encrypted_client_password",
  "g-recaptcha-response": "recaptcha_token",
  "remember": true
}</code></pre>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Success Response (200):</h4>
            <div class="code-block">
                <pre class="language-json"><code>{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "email": "john@example.com",
    "username": "john_doe",
    "first_name": "John",
    "last_name": "Doe",
    "role": "user",
    "is_2fa_enabled": true,
    "last_login": "2025-08-07T10:30:00Z"
  },
  "expires_at": "2025-08-07T22:30:00Z",
  "token_type": "Bearer",
  "security_features": {
    "2fa_enabled": true,
    "account_locked": false,
    "remaining_attempts": 5,
    "lockout_until": null
  }
}</code></pre>
            </div>
        </div>

        <div class="bg-red-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Error Responses:</h4>
            <div class="code-block mb-3">
                <pre class="language-json"><code>// 401 - Invalid Credentials
{
  "error": "Invalid credentials",
  "attempts_remaining": 3,
  "lockout_warning": "Account will be locked after 2 more failed attempts"
}

// 423 - Account Locked
{
  "error": "Account temporarily locked due to multiple failed attempts",
  "lockout_until": "2025-08-07T11:00:00Z",
  "remaining_minutes": 25
}

// 429 - Rate Limited
{
  "error": "Too many login attempts. Please try again in 45 seconds.",
  "retry_after": 45
}</code></pre>
            </div>
        </div>

        <h3 class="text-xl font-semibold mb-3">POST /sso/validate</h3>
        <p class="text-gray-600 mb-4">Validate an SSO token and get user information.</p>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Request Headers:</h4>
            <div class="code-block mb-3">
                <pre class="language-bash"><code>Content-Type: application/json
Authorization: Bearer CLIENT_TOKEN
X-Signature: sha256=HMAC_SHA256_SIGNATURE
X-Timestamp: 1640995200</code></pre>
            </div>

            <h4 class="font-semibold mb-2">Request Body:</h4>
            <div class="code-block">
                <pre class="language-json"><code>{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "client_id": "your_client_id",
  "client_secret": "your_client_secret",
  "timestamp": 1640995200
}</code></pre>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Response:</h4>
            <div class="code-block">
                <pre class="language-json"><code>{
  "success": true,
  "user": {
    "id": 1,
    "username": "john_doe",
    "email": "john@example.com",
    "first_name": "John",
    "last_name": "Doe",
    "role": "user"
  },
  "expires_at": "2025-01-10T12:00:00Z"
}</code></pre>
            </div>
        </div>
    </section>

    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">User Management</h2>

        <h3 class="text-xl font-semibold mb-3">GET /user</h3>
        <p class="text-gray-600 mb-4">Get current authenticated user information.</p>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Headers:</h4>
            <div class="code-block">
                <pre class="language-bash"><code>Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...</code></pre>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Response:</h4>
            <div class="code-block">
                <pre class="language-json"><code>{
  "id": 1,
  "username": "john_doe",
  "email": "john@example.com",
  "first_name": "John",
  "last_name": "Doe",
  "role": "user",
  "is_active": true,
  "created_at": "2025-01-01T00:00:00Z"
}</code></pre>
            </div>
        </div>

        <h3 class="text-xl font-semibold mb-3">POST /register</h3>
        <p class="text-gray-600 mb-4">Register a new user account.</p>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Request Body:</h4>
            <div class="code-block">
                <pre class="language-json"><code>{
  "username": "new_user",
  "email": "user@example.com",
  "password": "secure_password",
  "first_name": "New",
  "last_name": "User"
}</code></pre>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Response:</h4>
            <div class="code-block">
                <pre class="language-json"><code>{
  "success": true,
  "user": {
    "id": 2,
    "username": "new_user",
    "email": "user@example.com",
    "first_name": "New",
    "last_name": "User",
    "role": "user",
    "is_active": true,
    "created_at": "2025-01-10T12:00:00Z"
  }
}</code></pre>
            </div>
        </div>
    </section>

    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">Client System Management</h2>

        <h3 class="text-xl font-semibold mb-3">GET /client-systems</h3>
        <p class="text-gray-600 mb-4">Get all registered client systems (Admin only).</p>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Response:</h4>
            <div class="code-block">
                <pre class="language-json"><code>{
  "success": true,
  "client_systems": [
    {
      "id": 1,
      "name": "Customer Portal",
      "url": "http://localhost:9000",
      "callback_url": "http://localhost:9000/cas/callback",
      "is_active": true,
      "users_online": 5,
      "sso_version": "v2.1.0",
      "status": "active",
      "icon": "fas fa-users",
      "color": "blue",
      "created_at": "2025-01-01T00:00:00Z"
    }
  ]
}</code></pre>
            </div>
        </div>

        <h3 class="text-xl font-semibold mb-3">POST /client-systems</h3>
        <p class="text-gray-600 mb-4">Register a new client system (Admin only).</p>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h4 class="font-semibold mb-2">Request Body:</h4>
            <div class="code-block">
                <pre class="language-json"><code>{
  "name": "New Application",
  "url": "http://localhost:4000",
  "callback_url": "http://localhost:4000/cas/callback",
  "client_secret": "generated_secret_key"
}</code></pre>
            </div>
        </div>
    </section>

    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">Error Responses</h2>

        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <h3 class="text-lg font-semibold mb-2">HTTP Status Codes</h3>
            <ul class="space-y-1 text-sm">
                <li><strong>200:</strong> Success</li>
                <li><strong>201:</strong> Created</li>
                <li><strong>400:</strong> Bad Request</li>
                <li><strong>401:</strong> Unauthorized</li>
                <li><strong>403:</strong> Forbidden</li>
                <li><strong>404:</strong> Not Found</li>
                <li><strong>500:</strong> Internal Server Error</li>
            </ul>
        </div>

        <h3 class="text-xl font-semibold mb-3">Error Response Format</h3>
        <div class="code-block mb-6">
            <pre class="language-json"><code>{
  "success": false,
  "error": "Authentication failed",
  "message": "Invalid username or password",
  "code": "AUTH_FAILED"
}</code></pre>
        </div>
    </section>

    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">Code Examples</h2>

        <h3 class="text-xl font-semibold mb-3">JavaScript (Fetch)</h3>
        <div class="code-block mb-6">
            <pre class="language-javascript"><code>// Generate SSO token
async function generateSSOToken(username, password) {
    const response = await fetch('http://localhost:5000/api/sso/token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username,
            password,
            client_id: 'your_client_id',
            client_username: 'your_client_username',
            client_password: 'your_client_password'
        })
    });

    const data = await response.json();
    return data;
}

// Validate token
async function validateToken(token) {
    const response = await fetch('http://localhost:5000/api/sso/validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            token,
            client_username: 'your_client_username',
            client_password: 'your_client_password'
        })
    });

    const data = await response.json();
    return data;
}</code></pre>
        </div>

        <h3 class="text-xl font-semibold mb-3">PHP (cURL)</h3>
        <div class="code-block mb-6">
            <pre class="language-php"><code><?php
// Generate SSO token
function generateSSOToken($username, $password) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'http://localhost:5000/api/sso/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'username' => $username,
            'password' => $password,
            'client_id' => 'your_client_id',
            'client_username' => 'your_client_username',
            'client_password' => 'your_client_password'
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}

// Validate token
function validateToken($token) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'http://localhost:5000/api/sso/validate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'token' => $token,
            'client_username' => 'your_client_username',
            'client_password' => 'your_client_password'
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}
?></code></pre>
        </div>

        <h3 class="text-xl font-semibold mb-3">Python (requests)</h3>
        <div class="code-block mb-6">
            <pre class="language-python"><code>import requests
import json

# Generate SSO token
def generate_sso_token(username, password):
    url = 'http://localhost:5000/api/sso/token'
    data = {
        'username': username,
        'password': password,
        'client_id': 'your_client_id',
        'client_username': 'your_client_username',
        'client_password': 'your_client_password'
    }

    response = requests.post(url, json=data)
    return response.json()

# Validate token
def validate_token(token):
    url = 'http://localhost:5000/api/sso/validate'
    data = {
        'token': token,
        'client_username': 'your_client_username',
        'client_password': 'your_client_password'
    }

    response = requests.post(url, json=data)
    return response.json()</code></pre>
        </div>
    </section>

    <!-- Rate Limiting -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">Rate Limiting</h2>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <h3 class="text-lg font-semibold mb-2">Current Limits</h3>
            <ul class="space-y-1 text-sm">
                <li><strong>Authentication endpoints:</strong> 10 requests per minute per IP</li>
                <li><strong>Token validation:</strong> 100 requests per minute per client</li>
                <li><strong>User management:</strong> 50 requests per minute per user</li>
            </ul>
        </div>
    </section>

    <div class="bg-blue-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Next Steps</h2>
        <ul class="space-y-2">
            <li>• <a href="{{ route('docs.laravel') }}" class="text-blue-600 hover:text-blue-800">Laravel Integration Guide</a></li>
            <li>• <a href="{{ route('docs.examples') }}" class="text-blue-600 hover:text-blue-800">View Code Examples</a></li>
            <li>• <a href="/" class="text-blue-600 hover:text-blue-800">Test with CAS Dashboard</a></li>
        </ul>
    </div>
</div>
@endsection
