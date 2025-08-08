@extends('public.documentation.layout')

@section('title', $troubleshootingGuide['title'] . ' - CAS Documentation')
@section('description', $troubleshootingGuide['description'])

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $troubleshootingGuide['title'] }}</h1>
        <p class="text-lg text-gray-600">{{ $troubleshootingGuide['description'] }}</p>
    </div>

    <!-- Quick Navigation -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-yellow-900 mb-4">
            <i class="fas fa-tools mr-2"></i>Quick Issue Navigation
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($troubleshootingGuide['sections'] as $key => $title)
                <a href="#{{ $key }}" class="text-yellow-700 hover:text-yellow-900 hover:underline">
                    <i class="fas fa-arrow-right mr-2"></i>{{ $title }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Emergency Actions -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-red-900 mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>Emergency Actions
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="bg-red-100 rounded-lg p-4 mb-2">
                    <i class="fas fa-server text-red-600 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-red-800">System Down</h3>
                </div>
                <p class="text-sm text-red-700">Check server status and restart services</p>
            </div>
            <div class="text-center">
                <div class="bg-red-100 rounded-lg p-4 mb-2">
                    <i class="fas fa-database text-red-600 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-red-800">Database Issues</h3>
                </div>
                <p class="text-sm text-red-700">Verify database connection and credentials</p>
            </div>
            <div class="text-center">
                <div class="bg-red-100 rounded-lg p-4 mb-2">
                    <i class="fas fa-lock text-red-600 text-2xl mb-2"></i>
                    <h3 class="font-semibold text-red-800">Login Failures</h3>
                </div>
                <p class="text-sm text-red-700">Check authentication logs and user accounts</p>
            </div>
        </div>
    </div>

    <!-- Authentication Issues -->
    <section id="authentication" class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Authentication Issues</h2>
        
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user-times text-red-600 mr-2"></i>Users Cannot Login
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Symptoms:</h4>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>Login form shows "Invalid credentials" error</li>
                            <li>reCAPTCHA validation failures</li>
                            <li>Account lockout messages</li>
                            <li>2FA verification issues</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Solutions:</h4>
                        <div class="code-block">
                            <pre><code># Check user account status
php artisan cas:check-user username@example.com

# Reset account lockout
php artisan cas:unlock-user username@example.com

# Check authentication logs
tail -f storage/logs/laravel.log | grep "login"

# Test database connection
php artisan cas:test-db</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-key text-orange-600 mr-2"></i>SSO Token Issues
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Common Problems:</h4>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>JWT token validation failures</li>
                            <li>HMAC signature mismatches</li>
                            <li>Token expiration issues</li>
                            <li>Client system authentication errors</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Debug Commands:</h4>
                        <div class="code-block">
                            <pre><code># Validate JWT token
php artisan cas:validate-token "your-jwt-token-here"

# Check client system credentials
php artisan cas:test-client client-system-id

# Regenerate client secrets
php artisan cas:regenerate-secrets client-system-id

# Check HMAC signature validation
php artisan cas:verify-signature payload signature</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Database Connection Problems -->
    <section id="database" class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Database Connection Problems</h2>
        
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-database text-red-600 mr-2"></i>Connection Failures
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Check Database Status:</h4>
                        <div class="code-block">
                            <pre><code># Test database connection
php artisan cas:db-status

# Check PostgreSQL service
sudo systemctl status postgresql

# Verify database credentials
psql -h $PGHOST -U $PGUSER -d $PGDATABASE -c "SELECT version();"

# Check connection pool
php artisan cas:connection-pool</code></pre>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Common Issues:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded">
                                <h5 class="font-semibold text-gray-800 mb-2">SSL Connection Issues</h5>
                                <p class="text-sm text-gray-600 mb-2">Update .env file:</p>
                                <code class="text-xs">DB_SSLMODE=require</code>
                            </div>
                            <div class="bg-gray-50 p-4 rounded">
                                <h5 class="font-semibold text-gray-800 mb-2">Schema Not Found</h5>
                                <p class="text-sm text-gray-600 mb-2">Run migrations:</p>
                                <code class="text-xs">php artisan migrate</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-table text-blue-600 mr-2"></i>Migration and Schema Issues
                </h3>
                
                <div class="code-block">
                    <pre><code># Check migration status
php artisan migrate:status

# Reset and rebuild database
php artisan migrate:fresh --seed

# Repair specific table
php artisan cas:repair-table table_name

# Check schema integrity
php artisan cas:check-schema</code></pre>
                </div>
            </div>
        </div>
    </section>

    <!-- SSL/Certificate Issues -->
    <section id="ssl" class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">SSL/Certificate Issues</h2>
        
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-certificate text-green-600 mr-2"></i>Certificate Problems
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Check Certificate Status:</h4>
                        <div class="code-block">
                            <pre><code># Check certificate expiration
openssl x509 -in /path/to/certificate.crt -text -noout | grep "Not After"

# Test SSL connection
openssl s_client -connect cas.yourdomain.com:443 -servername cas.yourdomain.com

# Verify certificate chain
openssl verify -CAfile /path/to/ca-bundle.crt /path/to/certificate.crt

# Renew Let's Encrypt certificate
certbot renew --dry-run</code></pre>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                        <h4 class="font-semibold text-yellow-800 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Common SSL Errors
                        </h4>
                        <ul class="list-disc list-inside text-yellow-700 space-y-1">
                            <li><strong>ERR_CERT_AUTHORITY_INVALID:</strong> Untrusted certificate authority</li>
                            <li><strong>ERR_CERT_DATE_INVALID:</strong> Certificate expired or not yet valid</li>
                            <li><strong>ERR_SSL_PROTOCOL_ERROR:</strong> SSL/TLS protocol mismatch</li>
                            <li><strong>ERR_CERT_COMMON_NAME_INVALID:</strong> Domain mismatch in certificate</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Performance Problems -->
    <section id="performance" class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Performance Problems</h2>
        
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-tachometer-alt text-blue-600 mr-2"></i>Slow Response Times
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Performance Monitoring:</h4>
                        <div class="code-block">
                            <pre><code># Check application performance
php artisan cas:performance-check

# Monitor database queries
php artisan cas:query-monitor

# Check Redis cache status
redis-cli info stats

# Monitor server resources
htop
free -h
df -h</code></pre>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded">
                            <h5 class="font-semibold text-blue-800 mb-2">Cache Optimization</h5>
                            <div class="code-block">
                                <pre><code># Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize</code></pre>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded">
                            <h5 class="font-semibold text-green-800 mb-2">Database Optimization</h5>
                            <div class="code-block">
                                <pre><code># Analyze slow queries
php artisan cas:slow-queries

# Optimize database
php artisan cas:optimize-db</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Logging and Debugging -->
    <section id="logging" class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Logging and Debugging</h2>
        
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-file-alt text-purple-600 mr-2"></i>Log File Locations
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Application Logs</h4>
                            <ul class="text-sm text-gray-600 space-y-1 font-mono">
                                <li>Laravel: <code>storage/logs/laravel.log</code></li>
                                <li>Authentication: <code>storage/logs/auth.log</code></li>
                                <li>SSO Events: <code>storage/logs/sso.log</code></li>
                                <li>Audit Trail: <code>storage/logs/audit.log</code></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">System Logs</h4>
                            <ul class="text-sm text-gray-600 space-y-1 font-mono">
                                <li>Nginx: <code>/var/log/nginx/</code></li>
                                <li>PostgreSQL: <code>/var/log/postgresql/</code></li>
                                <li>System: <code>/var/log/syslog</code></li>
                                <li>Security: <code>/var/log/auth.log</code></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Debug Commands:</h4>
                        <div class="code-block">
                            <pre><code># Enable debug mode (development only)
php artisan cas:debug on

# Monitor logs in real-time
tail -f storage/logs/laravel.log

# Search for specific errors
grep -n "ERROR" storage/logs/laravel.log | tail -20

# Check log file sizes
du -sh storage/logs/

# Rotate log files
php artisan cas:rotate-logs</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Recovery -->
    <section id="recovery" class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">System Recovery</h2>
        
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-redo text-green-600 mr-2"></i>Recovery Procedures
                </h3>
                
                <div class="space-y-6">
                    <div class="bg-red-50 border border-red-200 rounded p-4">
                        <h4 class="font-semibold text-red-800 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Complete System Failure
                        </h4>
                        <div class="code-block">
                            <pre><code># Emergency recovery steps
1. sudo systemctl restart nginx
2. sudo systemctl restart postgresql
3. sudo systemctl restart redis
4. php artisan queue:restart
5. php artisan cas:health-check</code></pre>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded p-4">
                        <h4 class="font-semibold text-blue-800 mb-2">
                            <i class="fas fa-database mr-2"></i>Database Recovery
                        </h4>
                        <div class="code-block">
                            <pre><code># Restore from backup
pg_restore -h $PGHOST -U $PGUSER -d $PGDATABASE backup_file.sql

# Verify data integrity
php artisan cas:verify-data

# Rebuild indexes
php artisan cas:rebuild-indexes</code></pre>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded p-4">
                        <h4 class="font-semibold text-green-800 mb-2">
                            <i class="fas fa-shield-alt mr-2"></i>Security Incident Response
                        </h4>
                        <ul class="list-disc list-inside text-green-700 space-y-1">
                            <li>Immediately change all admin passwords</li>
                            <li>Regenerate all JWT secrets and client credentials</li>
                            <li>Review audit logs for suspicious activity</li>
                            <li>Update IP whitelist to block threats</li>
                            <li>Force logout of all active sessions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Support Contact -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-life-ring mr-2"></i>Additional Support
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="bg-blue-100 rounded-lg p-4 mb-3">
                    <i class="fas fa-book text-blue-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Documentation</h4>
                <p class="text-sm text-gray-600">Complete guides and API reference</p>
                <a href="/docs" class="text-blue-600 hover:text-blue-800 text-sm">Browse Docs</a>
            </div>
            <div class="text-center">
                <div class="bg-green-100 rounded-lg p-4 mb-3">
                    <i class="fas fa-code text-green-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Code Examples</h4>
                <p class="text-sm text-gray-600">Integration examples and samples</p>
                <a href="/docs/examples" class="text-green-600 hover:text-green-800 text-sm">View Examples</a>
            </div>
            <div class="text-center">
                <div class="bg-purple-100 rounded-lg p-4 mb-3">
                    <i class="fas fa-cogs text-purple-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">System Status</h4>
                <p class="text-sm text-gray-600">Check system health and status</p>
                <a href="/health" class="text-purple-600 hover:text-purple-800 text-sm">Health Check</a>
            </div>
        </div>
    </div>
</div>
@endsection