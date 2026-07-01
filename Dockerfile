FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    postgresql-dev \
    nginx \
    supervisor \
    bash \
    nodejs \
    npm \
    openssl

RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    xml \
    soap \
    intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www

# Run the php-fpm pool as the unprivileged "www" user that owns the app files
# (and that supervisor uses for the queue/scheduler workers), so file-driver
# sessions, cache, and logs under storage/ are writable. The stock php-fpm image
# runs the pool as www-data, which cannot write the www-owned (775) storage tree.
RUN sed -i 's/^user = .*/user = www/; s/^group = .*/group = www/' /usr/local/etc/php-fpm.d/www.conf

# .env and other secrets are excluded via .dockerignore, so the build context
# copied here never contains credentials. Inject env at runtime (env_file).
COPY --chown=www:www . .
RUN composer install --no-dev --optimize-autoloader --no-interaction

# npm (run as root) drops privileges to the workdir OWNER, so the workdir node
# itself must be www-owned (COPY --chown only chowns the copied contents, not the
# /var/www/html dir node, and composer ran as root). Chown the tree before npm.
RUN chown -R www:www /var/www/html && npm install --no-audit --no-fund && npm run build

RUN chown -R www:www /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap/cache

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN mkdir -p /var/log/supervisor && \
    chown -R www:www /var/log/supervisor

EXPOSE 80

# NOTE: the container entrypoint runs supervisord as root (it must chown
# storage/cache at boot, bind nginx on :80, and supervise the php-fpm master).
# php-fpm worker processes and the queue/scheduler programs already drop to the
# unprivileged "www" user via the supervisor program configs, so the actual
# application code does not execute as root. Do NOT set a global `USER www`
# here: it breaks supervisord's privileged boot steps. Privilege separation is
# handled per-program in docker/supervisor/supervisord.conf.
#USER www

HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
