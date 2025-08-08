@extends('public.documentation.layout')

@section('title', 'Python/Django CAS SSO Integration Guide - Enhanced Architecture')
@section('description', 'Complete guide for integrating Python/Django applications with our modernized CAS Single Sign-On system featuring Admin/User/Public separation and PostgreSQL multi-schema design.')

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="bg-yellow-100 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                    <i class="fab fa-python text-yellow-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $pythonGuide['title'] }}</h1>
                    <p class="text-gray-600 mt-1">{{ $pythonGuide['description'] }}</p>
                </div>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Enhanced Architecture:</strong> Our CAS system now features modular Admin/User/Public separation with Livewire components, PostgreSQL multi-schema design, and enhanced JWT authentication.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4 text-sm text-gray-600">
                <span><i class="fas fa-clock mr-1"></i>Setup time: 15 minutes</span>
                <span><i class="fas fa-code mr-1"></i>Difficulty: Medium</span>
                <span><i class="fas fa-tag mr-1"></i>Python 3.8+ / Django 3.2+</span>
            </div>
        </div>

        <!-- Installation -->
        <section id="installation" class="mb-12">
            <h2 class="text-2xl font-bold mb-4">1. Installation</h2>

            <div class="code-block mb-6">
                <pre class="language-bash"><code>pip install requests pyjwt cryptography psycopg2-binary django</code></pre>
            </div>

            <h3 class="text-xl font-semibold mb-3">Project Structure</h3>
            <div class="code-block mb-6">
            <pre class="language-text"><code>your-django-project/
├── cas_client/
│   ├── __init__.py
│   ├── client.py          # CAS client service
│   ├── middleware.py      # CAS authentication middleware
│   └── views.py           # CAS authentication views
├── settings/
│   └── cas_config.py      # CAS configuration
└── requirements.txt       # Dependencies</code></pre>
            </div>
        </section>

        <!-- Configuration -->
        <section id="configuration" class="mb-12">
            <h2 class="text-2xl font-bold mb-4">2. Configuration</h2>

            <h3 class="text-xl font-semibold mb-3">Django Settings</h3>
            <p class="text-gray-700 mb-4">Add to your <code class="bg-gray-100 px-2 py-1 rounded">settings.py</code>:</p>

            <div class="code-block mb-6">
            <pre class="language-python"><code># CAS Configuration - Enhanced Architecture
CAS_SERVER_URL = os.environ.get('CAS_SERVER_URL', 'https://your-cas-server.com')
CAS_CLIENT_ID = os.environ.get('CAS_CLIENT_ID')
CAS_CLIENT_SECRET = os.environ.get('CAS_CLIENT_SECRET')
CAS_SIGNATURE_SECRET = os.environ.get('CAS_SIGNATURE_SECRET')
CAS_CALLBACK_URL = os.environ.get('CAS_CALLBACK_URL')

# New Route Endpoints - Updated Architecture
CAS_ENDPOINTS = {
    'sso_token': '/api/sso/token',           # Enhanced client credentials
    'sso_validate': '/api/sso/validate',     # Token validation
    'callback': '/auth/sso/callback',        # SSO callback
    'user_dashboard': '/user/dashboard',     # User dashboard
    'logout': '/auth/logout'                 # Logout endpoint
}

# Enhanced Security Settings
CAS_SECURITY = {
    'token_expiry': 3600,                    # 1 hour
    'hmac_algorithm': 'sha256',              # HMAC-SHA256
    'ip_whitelist_enabled': True,            # IP whitelist support
    'audit_logging': True,                   # Comprehensive logging
    'rate_limiting': True                    # Rate limiting
}

# PostgreSQL Schema Configuration
CAS_DATABASE = {
    'schemas': {
        'admin': 'cas_admin',
        'user': 'cas_user',
        'public': 'cas_public',
        'audit': 'cas_audit'
    }
}

# Add to MIDDLEWARE
MIDDLEWARE = [
    # ... existing middleware
    'cas_client.middleware.CasAuthenticationMiddleware',
]

# Add to INSTALLED_APPS
INSTALLED_APPS = [
    # ... existing apps
    'cas_client',
]</code></pre>
            </div>

            <h3 class="text-xl font-semibold mb-3">Environment Variables</h3>
            <div class="code-block mb-6">
            <pre class="language-env"><code># CAS Configuration - Enhanced Architecture
CAS_SERVER_URL=https://your-cas-server.com
CAS_CLIENT_ID=your-client-id
CAS_CLIENT_SECRET=your-client-secret
CAS_SIGNATURE_SECRET=your-signature-secret
CAS_CALLBACK_URL=http://your-app.com/auth/cas/callback

# Database Configuration
CAS_DB_HOST=127.0.0.1
CAS_DB_PORT=5432
CAS_DB_NAME=cas_system
CAS_DB_USER=cas_user
CAS_DB_PASSWORD=secure_password</code></pre>
            </div>
        </section>

        <!-- CAS Client Service -->
        <section id="service" class="mb-12">
            <h2 class="text-2xl font-bold mb-4">3. CAS Client Service</h2>

            <p class="text-gray-700 mb-4">Create <code class="bg-gray-100 px-2 py-1 rounded">cas_client/client.py</code>:</p>

            <div class="code-block mb-6">
            <pre class="language-python"><code>import requests
import hashlib
import hmac
import json
import time
from urllib.parse import urlencode
from django.conf import settings
import jwt

class CasClient:
    def __init__(self):
        self.base_url = settings.CAS_SERVER_URL
        self.client_id = settings.CAS_CLIENT_ID
        self.client_secret = settings.CAS_CLIENT_SECRET
        self.signature_secret = settings.CAS_SIGNATURE_SECRET

    def generate_signature(self, data):
        """Generate HMAC-SHA256 signature for enhanced security"""
        sorted_keys = sorted(data.keys())
        query_string = '&'.join([f"{key}={data[key]}" for key in sorted_keys])

        return hmac.new(
            self.signature_secret.encode('utf-8'),
            query_string.encode('utf-8'),
            hashlib.sha256
        ).hexdigest()

    def request_token(self, username):
        """Enhanced token request with HMAC signature"""
        timestamp = int(time.time())
        request_data = {
            'client_id': self.client_id,
            'client_secret': self.client_secret,
            'username': username,
            'timestamp': timestamp
        }

        # Generate HMAC-SHA256 signature
        signature = self.generate_signature(request_data)
        request_data['signature'] = signature

        try:
            response = requests.post(
                f"{self.base_url}{settings.CAS_ENDPOINTS['sso_token']}",
                json=request_data,
                headers={
                    'Content-Type': 'application/json',
                    'X-CAS-Client-ID': self.client_id
                },
                timeout=30
            )
            response.raise_for_status()
            return response.json()
        except requests.RequestException as e:
            raise Exception(f"Failed to obtain CAS token: {str(e)}")

    def validate_token(self, token):
        """Enhanced token validation"""
        request_data = {
            'token': token,
            'timestamp': int(time.time())
        }

        signature = self.generate_signature(request_data)
        request_data['signature'] = signature

        try:
            response = requests.post(
                f"{self.base_url}{settings.CAS_ENDPOINTS['sso_validate']}",
                json=request_data,
                headers={
                    'Content-Type': 'application/json',
                    'X-CAS-Client-ID': self.client_id
                },
                timeout=30
            )
            response.raise_for_status()
            return response.json()
        except requests.RequestException as e:
            return {'valid': False, 'error': f'Token validation failed: {str(e)}'}

    def get_login_url(self, return_url=None):
        """Generate SSO login URL"""
        params = {
            'client_id': self.client_id,
            'redirect_uri': settings.CAS_CALLBACK_URL,
        }
        if return_url:
            params['return_url'] = return_url

        return f"{self.base_url}/auth/sso?{urlencode(params)}"

    def process_callback(self, callback_data):
        """Process SSO callback with enhanced validation"""
        try:
            token = callback_data.get('token')
            if not token:
                return {'success': False, 'error': 'Missing token'}

            validation_result = self.validate_token(token)

            if validation_result.get('valid'):
                return {
                    'success': True,
                    'user': validation_result.get('user'),
                    'token': token,
                    'return_url': callback_data.get('return_url', '/')
                }
            else:
                return {
                    'success': False,
                    'error': validation_result.get('error', 'Invalid token')
                }
        except Exception as e:
            return {'success': False, 'error': str(e)}

# Global instance
cas_client = CasClient()</code></pre>
            </div>
        </section>

        <!-- Middleware -->
        <section id="middleware" class="mb-12">
            <h2 class="text-2xl font-bold mb-4">4. Authentication Middleware</h2>

            <p class="text-gray-700 mb-4">Create <code class="bg-gray-100 px-2 py-1 rounded">cas_client/middleware.py</code>:</p>

            <div class="code-block mb-6">
            <pre class="language-python"><code>from django.shortcuts import redirect
from django.urls import reverse
from django.http import JsonResponse
from django.contrib.auth.models import AnonymousUser
from .client import cas_client

class CasAuthenticationMiddleware:
    """Enhanced CAS Authentication Middleware"""

    def __init__(self, get_response):
        self.get_response = get_response
        # Protected URL patterns that require CAS authentication
        self.protected_patterns = [
            '/user/dashboard',
            '/admin/systems',
            '/api/profile',
            '/protected/'
        ]

    def __call__(self, request):
        # Check if the request path requires CAS authentication
        if self.requires_cas_auth(request.path):
            if not self.is_authenticated(request):
                return self.handle_unauthenticated(request)

        response = self.get_response(request)
        return response

    def requires_cas_auth(self, path):
        """Check if path requires CAS authentication"""
        return any(path.startswith(pattern) for pattern in self.protected_patterns)

    def is_authenticated(self, request):
        """Check if user is authenticated via CAS"""
        cas_user = request.session.get('cas_user')
        cas_token = request.session.get('cas_token')

        if not cas_user or not cas_token:
            return False

        # Validate existing token
        try:
            validation = cas_client.validate_token(cas_token)
            if validation.get('valid'):
                # Update request with user info
                request.cas_user = cas_user
                request.cas_token = cas_token
                return True
            else:
                # Clear invalid session
                request.session.pop('cas_user', None)
                request.session.pop('cas_token', None)
                return False
        except Exception:
            return False

    def handle_unauthenticated(self, request):
        """Handle unauthenticated requests"""
        # Store return URL for after authentication
        request.session['cas_return_url'] = request.get_full_path()

        # Check if this is an AJAX request
        if request.headers.get('X-Requested-With') == 'XMLHttpRequest':
            return JsonResponse({
                'error': 'Authentication required',
                'login_url': cas_client.get_login_url(request.get_full_path())
            }, status=401)

        # Redirect to CAS login
        login_url = cas_client.get_login_url(request.get_full_path())
        return redirect(login_url)

def cas_required(view_func):
    """Decorator for views that require CAS authentication"""
    def wrapper(request, *args, **kwargs):
        cas_user = request.session.get('cas_user')
        cas_token = request.session.get('cas_token')

        if not cas_user or not cas_token:
            return redirect(cas_client.get_login_url(request.get_full_path()))

        # Validate token
        validation = cas_client.validate_token(cas_token)
        if not validation.get('valid'):
            request.session.pop('cas_user', None)
            request.session.pop('cas_token', None)
            return redirect(cas_client.get_login_url(request.get_full_path()))

        # Add user info to request
        request.cas_user = cas_user
        request.cas_token = cas_token

        return view_func(request, *args, **kwargs)

    return wrapper</code></pre>
            </div>
        </section>

        <!-- Views Implementation -->
        <section id="views" class="mb-12">
            <h2 class="text-2xl font-bold mb-4">5. Views Implementation</h2>

            <p class="text-gray-700 mb-4">Create <code class="bg-gray-100 px-2 py-1 rounded">cas_client/views.py</code>:</p>

            <div class="code-block mb-6">
            <pre class="language-python"><code>from django.shortcuts import render, redirect
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.views.decorators.http import require_http_methods
from django.contrib import messages
from .client import cas_client
from .middleware import cas_required
import json

@csrf_exempt
@require_http_methods(["GET"])
def cas_callback(request):
    """Enhanced SSO callback handler"""
    try:
        token = request.GET.get('token')
        return_url = request.GET.get('return_url', '/user/dashboard')

        if not token:
            messages.error(request, 'Authentication failed: Missing token')
            return redirect('/auth/login')

        # Process callback with enhanced validation
        result = cas_client.process_callback({
            'token': token,
            'return_url': return_url
        })

        if result['success']:
            # Store user session
            request.session['cas_user'] = result['user']
            request.session['cas_token'] = result['token']

            messages.success(request, f"Welcome back, {result['user']['username']}!")
            return redirect(result['return_url'])
        else:
            messages.error(request, f"Authentication failed: {result['error']}")
            return redirect('/auth/login')

    except Exception as e:
        messages.error(request, f'Callback processing failed: {str(e)}')
        return redirect('/auth/login')

@require_http_methods(["POST"])
def cas_logout(request):
    """Enhanced logout handler"""
    # Clear CAS session
    request.session.pop('cas_user', None)
    request.session.pop('cas_token', None)
    request.session.flush()

    if request.headers.get('X-Requested-With') == 'XMLHttpRequest':
        return JsonResponse({
            'success': True,
            'message': 'Logged out successfully',
            'redirect': '/auth/login'
        })

    messages.success(request, 'You have been logged out successfully')
    return redirect('/auth/login')

@cas_required
def user_dashboard(request):
    """Enhanced user dashboard with new architecture"""
    user = request.cas_user

    context = {
        'user': user,
        'features': [
            'Client System Linking',
            'One-Click SSO Login',
            'Profile Management',
            'System Access Control'
        ],
        'architecture': {
            'separation': 'Admin/User/Public',
            'database': 'PostgreSQL Multi-Schema',
            'performance': 'Livewire Optimized'
        }
    }

    return render(request, 'cas_client/dashboard.html', context)

@cas_required
def user_profile_api(request):
    """Protected API endpoint for user profile"""
    user = request.cas_user

    return JsonResponse({
        'success': True,
        'profile': {
            'id': user['id'],
            'username': user['username'],
            'email': user.get('email'),
            'roles': user.get('roles', []),
            'last_login': user.get('last_login'),
            'client_systems': user.get('client_systems', [])
        }
    })

def health_check(request):
    """Health check with architecture info"""
    return JsonResponse({
        'status': 'healthy',
        'timestamp': str(timezone.now()),
        'services': {
            'cas_client': 'connected',
            'session_store': 'active',
            'authentication': 'enabled'
        },
        'architecture': {
            'type': 'CAS Client - Django/Python',
            'version': '2.0',
            'features': [
                'Enhanced JWT Authentication',
                'HMAC-SHA256 Signature Validation',
                'Admin/User/Public Route Separation',
                'PostgreSQL Multi-Schema Support'
            ]
        }
    })</code></pre>
            </div>
        </section>

        <!-- URL Configuration -->
        <section id="urls" class="mb-12">
            <h2 class="text-2xl font-bold mb-4">6. URL Configuration</h2>

            <p class="text-gray-700 mb-4">Create <code class="bg-gray-100 px-2 py-1 rounded">cas_client/urls.py</code>:</p>

            <div class="code-block mb-6">
            <pre class="language-python"><code>from django.urls import path
from . import views

app_name = 'cas_client'

urlpatterns = [
    # Enhanced authentication URLs
    path('auth/cas/callback/', views.cas_callback, name='cas_callback'),
    path('auth/logout/', views.cas_logout, name='cas_logout'),

    # Enhanced user dashboard - new architecture
    path('user/dashboard/', views.user_dashboard, name='user_dashboard'),

    # Protected API endpoints
    path('api/profile/', views.user_profile_api, name='user_profile_api'),

    # Health check
    path('health/', views.health_check, name='health_check'),
]</code></pre>
            </div>

            <p class="text-gray-700 mb-4">Add to your main <code class="bg-gray-100 px-2 py-1 rounded">urls.py</code>:</p>

            <div class="code-block mb-6">
            <pre class="language-python"><code>from django.contrib import admin
from django.urls import path, include

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', include('cas_client.urls')),
    # ... other URL patterns
]</code></pre>
            </div>
        </section>

        <!-- Template Example -->
        <section id="templates" class="mb-12">
            <h2 class="text-2xl font-bold mb-4">7. Template Example</h2>

            <p class="text-gray-700 mb-4">Create <code class="bg-gray-100 px-2 py-1 rounded">templates/cas_client/dashboard.html</code>:</p>

            <div class="code-block mb-6">
            <pre class="language-html"><code><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - CAS Client</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-green-600 text-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">👤 User Dashboard</h1>
                        <p class="text-green-100">Enhanced CAS Architecture</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-green-100">{{ $user['username'] ?? 'username' }}</span>
                        <form method="post" action="/cas_client/logout/" class="inline">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="bg-green-700 px-4 py-2 rounded hover:bg-green-800">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 py-8">
            <!-- Architecture Info -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">System Architecture</h2>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded">
                        <h3 class="font-semibold text-blue-900">Clean Separation</h3>
                        <p class="text-sm text-blue-700">Modular Design</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded">
                        <h3 class="font-semibold text-green-900">PostgreSQL</h3>
                        <p class="text-sm text-green-700">Enterprise Database</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded">
                        <h3 class="font-semibold text-purple-900">Optimized</h3>
                        <p class="text-sm text-purple-700">High Performance</p>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Available Features</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="flex items-center p-3 bg-gray-50 rounded">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Single Sign-On Authentication</span>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>JWT Token Management</span>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Secure Client Authentication</span>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Comprehensive Audit Logging</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html></code></pre>
            </div>
        </section>

        <!-- Testing -->
        <section id="testing" class="mb-12">
            <h2 class="text-2xl font-bold mb-4">8. Testing Your Integration</h2>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-900 mb-4">Test Checklist</h3>
                <ul class="space-y-2 text-yellow-800">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        CAS middleware intercepts protected URLs
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        SSO authentication flow completes
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Token validation and signature verification
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        User dashboard (/user/dashboard) accessible
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Session management works correctly
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Logout functionality clears session
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
                <a href="{{ route('docs.section', 'javascript') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    JavaScript Integration →
                </a>
            </div>
        </div>
    </div>
@endsection
