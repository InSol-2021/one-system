@extends('public.documentation.layout')

@section('title', 'Security Features - CAS Documentation')
@section('description', 'Comprehensive security documentation for enterprise-grade CAS authentication system.')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            <i class="fas fa-shield-alt text-red-600 mr-3"></i>
            Enterprise Security Features
        </h1>
        <p class="text-gray-600">Comprehensive security documentation for the CAS authentication system</p>
        
        <!-- Security Score -->
        <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-award text-green-600 mr-2"></i>
                    <span class="font-bold text-green-800">Security Rating: 10/10 Enterprise-Grade</span>
                </div>
                <span class="text-green-600 text-sm">All critical security layers implemented</span>
            </div>
        </div>
    </div>

    <!-- Multi-layered Security Overview -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Multi-layered Security Architecture</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Layer 1: Bot Protection -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-robot text-green-600 mr-2"></i>
                    <h3 class="font-semibold text-green-800">Bot Protection</h3>
                </div>
                <ul class="text-sm text-green-700 space-y-1">
                    <li>• Google reCAPTCHA v3</li>
                    <li>• Invisible verification</li>
                    <li>• Score-based validation (0.5)</li>
                    <li>• No user interaction required</li>
                </ul>
            </div>

            <!-- Layer 2: Rate Limiting -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-tachometer-alt text-blue-600 mr-2"></i>
                    <h3 class="font-semibold text-blue-800">Rate Limiting</h3>
                </div>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• 5 requests per minute per IP</li>
                    <li>• Immediate protection</li>
                    <li>• 429 status with retry-after</li>
                    <li>• Prevents brute force attacks</li>
                </ul>
            </div>

            <!-- Layer 3: Account Lockout -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-ban text-red-600 mr-2"></i>
                    <h3 class="font-semibold text-red-800">Account Lockout</h3>
                </div>
                <ul class="text-sm text-red-700 space-y-1">
                    <li>• 5 failed attempts = 30min lock</li>
                    <li>• Progressive security</li>
                    <li>• Comprehensive audit logging</li>
                    <li>• Automatic timeout recovery</li>
                </ul>
            </div>

            <!-- Layer 4: Password Security -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-key text-purple-600 mr-2"></i>
                    <h3 class="font-semibold text-purple-800">Password Security</h3>
                </div>
                <ul class="text-sm text-purple-700 space-y-1">
                    <li>• Enterprise complexity rules</li>
                    <li>• Weak password detection</li>
                    <li>• bcrypt/scrypt hashing</li>
                    <li>• Character repetition limits</li>
                </ul>
            </div>

            <!-- Layer 5: Multi-Factor Auth -->
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-mobile-alt text-orange-600 mr-2"></i>
                    <h3 class="font-semibold text-orange-800">Multi-Factor Auth</h3>
                </div>
                <ul class="text-sm text-orange-700 space-y-1">
                    <li>• TOTP (Time-based codes)</li>
                    <li>• QR code enrollment</li>
                    <li>• Backup codes available</li>
                    <li>• Optional per-user basis</li>
                </ul>
            </div>

            <!-- Layer 6: Network Security -->
            <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-network-wired text-teal-600 mr-2"></i>
                    <h3 class="font-semibold text-teal-800">Network Security</h3>
                </div>
                <ul class="text-sm text-teal-700 space-y-1">
                    <li>• IP whitelisting system</li>
                    <li>• HMAC-SHA256 signatures</li>
                    <li>• Timestamp validation</li>
                    <li>• Anti-replay protection</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Password Requirements -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Password Complexity Requirements</h2>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-blue-800 mb-3">Required Elements</h3>
                    <ul class="text-blue-700 space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            Minimum 8 characters in length
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            At least 1 uppercase letter (A-Z)
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            At least 1 lowercase letter (a-z)
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            At least 1 number (0-9)
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            At least 1 special character (!@#$%^&*)
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold text-blue-800 mb-3">Security Restrictions</h3>
                    <ul class="text-blue-700 space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-times-circle text-red-600 mr-2"></i>
                            No common weak passwords
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-times-circle text-red-600 mr-2"></i>
                            No more than 3 consecutive identical chars
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-times-circle text-red-600 mr-2"></i>
                            No dictionary words or patterns
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                            Real-time strength validation
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Example passwords -->
            <div class="mt-6 p-4 bg-blue-100 rounded border border-blue-300">
                <h4 class="font-semibold text-blue-800 mb-2">Example Valid Passwords:</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <code class="text-green-700 font-mono">MySecure123!</code> ✓
                    </div>
                    <div>
                        <code class="text-green-700 font-mono">Enterprise#2024</code> ✓
                    </div>
                </div>
                
                <h4 class="font-semibold text-blue-800 mb-2 mt-3">Example Invalid Passwords:</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <code class="text-red-700 font-mono">password123</code> ✗ (too common)
                    </div>
                    <div>
                        <code class="text-red-700 font-mono">aaaa1111</code> ✗ (repetitive characters)
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Account Lockout Details -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Account Lockout System</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Lockout Process -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <h3 class="font-semibold text-red-800 mb-4">Lockout Process</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="bg-yellow-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1</div>
                        <div>
                            <div class="font-semibold text-red-700">Attempts 1-4</div>
                            <div class="text-red-600 text-sm">Warning messages with remaining attempts</div>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2</div>
                        <div>
                            <div class="font-semibold text-red-700">Attempt 5</div>
                            <div class="text-red-600 text-sm">Account locked for 30 minutes</div>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3</div>
                        <div>
                            <div class="font-semibold text-red-700">After Timeout</div>
                            <div class="text-red-600 text-sm">Account automatically unlocked</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- API Responses -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">API Responses</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-700 mb-1">Warning Response (401)</div>
                        <div class="code-block text-xs">
                            <pre class="language-json"><code>{
  "error": "Invalid credentials",
  "attempts_remaining": 3,
  "lockout_warning": "Account will lock after 2 more attempts"
}</code></pre>
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm font-semibold text-gray-700 mb-1">Locked Response (423)</div>
                        <div class="code-block text-xs">
                            <pre class="language-json"><code>{
  "error": "Account locked",
  "lockout_until": "2025-08-07T11:00:00Z",
  "remaining_minutes": 25
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HMAC Signature Security -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6">HMAC Signature Security</h2>
        
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- How it works -->
                <div>
                    <h3 class="font-semibold text-purple-800 mb-4">How HMAC Signatures Work</h3>
                    <ol class="text-purple-700 space-y-2">
                        <li>1. Generate Unix timestamp</li>
                        <li>2. Create payload: METHOD + URI + TIMESTAMP + BODY</li>
                        <li>3. Sign with HMAC-SHA256 using webhook secret</li>
                        <li>4. Include as X-Signature header</li>
                        <li>5. Server validates signature and timestamp</li>
                    </ol>
                </div>
                
                <!-- Security benefits -->
                <div>
                    <h3 class="font-semibold text-purple-800 mb-4">Security Benefits</h3>
                    <ul class="text-purple-700 space-y-2">
                        <li>• <strong>Prevents replay attacks</strong> with timestamps</li>
                        <li>• <strong>Ensures message integrity</strong> via hashing</li>
                        <li>• <strong>Authenticates sender</strong> with shared secret</li>
                        <li>• <strong>Detects tampering</strong> in transit</li>
                        <li>• <strong>Military-grade security</strong> standard</li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-purple-100 rounded border border-purple-300">
                <h4 class="font-semibold text-purple-800 mb-2">Example Signature Generation</h4>
                <div class="code-block">
                    <pre class="language-php"><code>$payload = 'POST/api/sso/token1640995200{"email":"user@example.com"}';
$signature = hash_hmac('sha256', $payload, $webhookSecret);
$header = 'X-Signature: sha256=' . $signature;</code></pre>
                </div>
            </div>
        </div>
    </section>

    <!-- Audit & Monitoring -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Comprehensive Audit & Monitoring</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Security Events -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-3">Security Events Logged</h3>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Login attempts (success/failure)</li>
                    <li>• Account lockouts and unlocks</li>
                    <li>• 2FA verification attempts</li>
                    <li>• Password reset requests</li>
                    <li>• IP whitelist violations</li>
                    <li>• Rate limiting triggers</li>
                    <li>• HMAC signature failures</li>
                </ul>
            </div>
            
            <!-- Data Collected -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-3">Audit Data Collected</h3>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• User ID and email</li>
                    <li>• IP address and geolocation</li>
                    <li>• User agent and device info</li>
                    <li>• Timestamp (UTC)</li>
                    <li>• Action details and context</li>
                    <li>• Success/failure status</li>
                    <li>• Security flags and warnings</li>
                </ul>
            </div>
            
            <!-- Monitoring Features -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-3">Monitoring Features</h3>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Real-time security dashboards</li>
                    <li>• Suspicious activity alerts</li>
                    <li>• Failed attempt tracking</li>
                    <li>• Geographic anomaly detection</li>
                    <li>• Export capabilities for SIEM</li>
                    <li>• Historical trend analysis</li>
                    <li>• Compliance reporting</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Best Practices -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Security Best Practices</h2>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-green-800 mb-3">For Administrators</h3>
                    <ul class="text-green-700 space-y-2">
                        <li>• Regularly review audit logs</li>
                        <li>• Monitor failed login attempts</li>
                        <li>• Keep IP whitelist updated</li>
                        <li>• Rotate webhook secrets periodically</li>
                        <li>• Enable 2FA for admin accounts</li>
                        <li>• Set up security alerts</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold text-green-800 mb-3">For Developers</h3>
                    <ul class="text-green-700 space-y-2">
                        <li>• Implement proper error handling</li>
                        <li>• Use HTTPS for all communications</li>
                        <li>• Validate HMAC signatures</li>
                        <li>• Handle rate limiting gracefully</li>
                        <li>• Store secrets securely</li>
                        <li>• Test security features regularly</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection