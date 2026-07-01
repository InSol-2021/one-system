@extends('public.documentation.layout')

@section('title', 'Deployment guide — One System SSO')
@section('description', 'Deploy One System SSO to production with Docker, Kubernetes, or traditional hosting.')

@section('content')
<div class="os-container !px-0 !max-w-none">

{{-- Hero --}}
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <p class="os-eyebrow mb-3">Advanced topics</p>
    <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Deployment guide</h1>
    <p class="text-lg text-[var(--color-muted)] leading-relaxed">Production-ready deployment configurations for One System SSO.</p>
</section>

{{-- Requirements --}}
<section class="mb-12" id="requirements">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">System requirements</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="os-card os-card-pad">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-3">Server</h3>
            <ul class="space-y-2 text-sm text-[var(--color-ink-2)]">
                <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--color-accent)] text-xs"></i>PHP 8.3+ with Laravel 11</li>
                <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--color-accent)] text-xs"></i>PostgreSQL 14+</li>
                <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--color-accent)] text-xs"></i>Redis 7+ for cache and sessions</li>
                <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--color-accent)] text-xs"></i>Nginx web server</li>
                <li class="flex items-center gap-2.5"><i class="fas fa-check text-[var(--color-accent)] text-xs"></i>Node.js 20+ for asset compilation</li>
            </ul>
        </div>
        <div class="os-card os-card-pad">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-3">Minimum resources</h3>
            <ul class="space-y-2 text-sm text-[var(--color-ink-2)]">
                <li class="flex items-center gap-2.5"><i class="fas fa-memory text-[var(--color-muted)] text-xs"></i>2 GB RAM</li>
                <li class="flex items-center gap-2.5"><i class="fas fa-microchip text-[var(--color-muted)] text-xs"></i>2 vCPU cores</li>
                <li class="flex items-center gap-2.5"><i class="fas fa-hdd text-[var(--color-muted)] text-xs"></i>20 GB SSD storage</li>
                <li class="flex items-center gap-2.5"><i class="fas fa-network-wired text-[var(--color-muted)] text-xs"></i>SSL/TLS certificate</li>
            </ul>
        </div>
    </div>
</section>

{{-- Docker --}}
<section class="mb-12" id="docker">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Docker deployment</h2>
    <p class="text-sm text-[var(--color-muted)] mb-4">The project includes a multi-service Docker Compose configuration for development and a production overlay.</p>

    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head">
            <span><i class="fab fa-docker mr-1.5"></i>docker-compose.yml</span>
            <span>YAML</span>
        </div>
        <pre><code>version: '3.8'
services:
  app:
    build:
      context: ./docker/nginx
    ports:
      - "80:80"
    depends_on:
      - php
      - redis

  php:
    build:
      context: ./docker/php
    volumes:
      - .:/var/www/html:delegated
    env_file:
      - .env

  redis:
    image: redis:7-alpine
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD}

volumes:
  redis_data:</code></pre>
    </div>

    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head">
            <span>terminal · build &amp; run</span>
            <span>bash</span>
        </div>
        <pre><code># Build and start all services
docker-compose up -d --build

# Generate the application key
docker-compose exec php php artisan key:generate

# Run migrations and seed the database
docker-compose exec php php artisan migrate --seed

# Build frontend assets
docker-compose run --rm node npm install &amp;&amp; npm run build</code></pre>
    </div>

    <div class="os-alert mb-6">
        <i class="fas fa-circle-info text-[var(--color-accent)] mt-0.5"></i>
        <div>
            <strong>Production overlay</strong> — use <code class="os-code-inline">docker-production.yml</code> for production. It adds Prometheus monitoring, Grafana dashboards, Fluentd logging, and queue workers with resource limits.
        </div>
    </div>
</section>

{{-- Kubernetes --}}
<section class="mb-12" id="kubernetes">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Kubernetes deployment</h2>
    <p class="text-sm text-[var(--color-muted)] mb-4">Deploy One System on Kubernetes using the standalone Dockerfile, which bundles PHP-FPM, Nginx, and Supervisor into a single image.</p>

    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head">
            <span>k8s/deployment.yaml</span>
            <span>YAML</span>
        </div>
        <pre><code>apiVersion: apps/v1
kind: Deployment
metadata:
  name: one-system-sso
spec:
  replicas: 2
  selector:
    matchLabels:
      app: one-system-sso
  template:
    metadata:
      labels:
        app: one-system-sso
    spec:
      containers:
        - name: one-system-sso
          image: your-registry/one-system-sso:latest
          ports:
            - containerPort: 80
          envFrom:
            - secretRef:
                name: one-system-sso-secrets
          resources:
            requests:
              cpu: "500m"
              memory: "512Mi"
            limits:
              cpu: "1000m"
              memory: "1Gi"
          livenessProbe:
            httpGet:
              path: /health
              port: 80
            initialDelaySeconds: 10
            periodSeconds: 30</code></pre>
    </div>

    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>terminal · build &amp; deploy</span>
            <span>bash</span>
        </div>
        <pre><code># Build the production image (uses the root Dockerfile)
docker build -t your-registry/one-system-sso:latest .

# Push to registry
docker push your-registry/one-system-sso:latest

# Deploy to the cluster
kubectl apply -f k8s/deployment.yaml</code></pre>
    </div>
</section>

{{-- Environment --}}
<section class="mb-12" id="environment">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Environment configuration</h2>
    <p class="text-sm text-[var(--color-muted)] mb-4">Set secrets through the environment — never commit them to source control. <code class="os-code-inline">JWT_SECRET</code> signs SSO tokens, <code class="os-code-inline">RECAPTCHAV3_*</code> configures reCAPTCHA, and <code class="os-code-inline">CORS_ALLOWED_ORIGINS</code> restricts which client domains may call the API.</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>.env · production</span>
            <span>env</span>
        </div>
        <pre><code>APP_NAME="One System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sso.yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=your_db_host
DB_DATABASE=one_system
DB_USERNAME=one_system_user
DB_PASSWORD=your_secure_password

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

REDIS_HOST=your_redis_host
REDIS_PASSWORD=your_redis_password

# SSO token signing (HS256)
JWT_SECRET=your_long_random_jwt_secret

# Restrict cross-origin API access to registered client domains
CORS_ALLOWED_ORIGINS=https://app.yourdomain.com,https://portal.yourdomain.com

# reCAPTCHA v3
RECAPTCHAV3_SITEKEY=your_recaptcha_site_key
RECAPTCHAV3_SECRET=your_recaptcha_secret</code></pre>
    </div>
</section>

{{-- Production checklist --}}
<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Production checklist</h2>
    <div class="space-y-2">
        <div class="os-card flex items-center gap-3 p-3 text-sm">
            <i class="fas fa-check-square text-[var(--color-accent)]"></i><span class="text-[var(--color-ink-2)]">Set <code class="os-code-inline">APP_DEBUG=false</code></span>
        </div>
        <div class="os-card flex items-center gap-3 p-3 text-sm">
            <i class="fas fa-check-square text-[var(--color-accent)]"></i><span class="text-[var(--color-ink-2)]">Serve over HTTPS with a valid SSL/TLS certificate</span>
        </div>
        <div class="os-card flex items-center gap-3 p-3 text-sm">
            <i class="fas fa-check-square text-[var(--color-accent)]"></i><span class="text-[var(--color-ink-2)]">Set a strong <code class="os-code-inline">JWT_SECRET</code> and strong database / Redis passwords</span>
        </div>
        <div class="os-card flex items-center gap-3 p-3 text-sm">
            <i class="fas fa-check-square text-[var(--color-accent)]"></i><span class="text-[var(--color-ink-2)]">Configure <code class="os-code-inline">CORS_ALLOWED_ORIGINS</code> for your registered client domains</span>
        </div>
        <div class="os-card flex items-center gap-3 p-3 text-sm">
            <i class="fas fa-check-square text-[var(--color-accent)]"></i><span class="text-[var(--color-ink-2)]">Set <code class="os-code-inline">RECAPTCHAV3_*</code> keys for the production domain</span>
        </div>
        <div class="os-card flex items-center gap-3 p-3 text-sm">
            <i class="fas fa-check-square text-[var(--color-accent)]"></i><span class="text-[var(--color-ink-2)]">Run the latest migrations (<code class="os-code-inline">php artisan migrate --force</code>) so admin/user auth guards are applied</span>
        </div>
        <div class="os-card flex items-center gap-3 p-3 text-sm">
            <i class="fas fa-check-square text-[var(--color-accent)]"></i><span class="text-[var(--color-ink-2)]">Run <code class="os-code-inline">php artisan config:cache</code> and <code class="os-code-inline">php artisan route:cache</code></span>
        </div>
        <div class="os-card flex items-center gap-3 p-3 text-sm">
            <i class="fas fa-check-square text-[var(--color-accent)]"></i><span class="text-[var(--color-ink-2)]">Set up automated database backups</span>
        </div>
        <div class="os-card flex items-center gap-3 p-3 text-sm">
            <i class="fas fa-check-square text-[var(--color-accent)]"></i><span class="text-[var(--color-ink-2)]">Enable application logging with daily rotation</span>
        </div>
        <div class="os-card flex items-center gap-3 p-3 text-sm">
            <i class="fas fa-check-square text-[var(--color-accent)]"></i><span class="text-[var(--color-ink-2)]">Whitelist server IPs for service-to-service token issuance</span>
        </div>
    </div>
</section>

</div>
@endsection
