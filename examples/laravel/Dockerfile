# Minimal image for the One System CAS Laravel sample.
# Uses the official PHP CLI image + the built-in artisan server on port 9101.
FROM php:8.4-cli-alpine

# System deps + PHP extensions Laravel/Guzzle/JWT need, plus pdo_sqlite for the
# LOCAL username/password store (App\Support\LocalUserStore -> database/app.db).
RUN apk add --no-cache git unzip libzip-dev oniguruma-dev sqlite-dev \
    && docker-php-ext-install zip bcmath pdo_sqlite \
    && rm -rf /var/cache/apk/*

# Composer (copied from the official composer image).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# The sample depends on the local package via a composer "path" repository at
# ../../packages/laravel-cas-client-package. For the image to resolve that path,
# build from the repo root so the packages/ dir is in the build context:
#
#   docker build -f one-system/examples/laravel/Dockerfile -t cas-laravel-sample one-system
#
# The Dockerfile copies both the example and the package into the image.
COPY examples/laravel /app
COPY packages/laravel-cas-client-package /packages/laravel-cas-client-package

# Re-point the composer path repo to the copied package location inside the image.
RUN sed -i 's#"url": "../../packages/laravel-cas-client-package"#"url": "/packages/laravel-cas-client-package"#' composer.json

# Install PHP dependencies (no dev) and prime the autoloader.
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Ensure writable runtime dirs. The SQLite file for local auth lives in
# database/ and must be writable at runtime (created/seeded on first request).
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache database \
    && chmod -R 777 storage bootstrap/cache database

EXPOSE 9101

# Generate an APP_KEY at runtime if none is provided, then serve.
# Unlike the other samples (which read process env directly), Laravel resolves
# config from its .env file. So at startup we overlay the container's env vars
# (compose-injected: CAS_*, APP_URL) onto .env — otherwise the .env.example
# placeholders would shadow the real deployment values.
CMD sh -c 'cp -n .env.example .env 2>/dev/null || true; \
           for k in APP_URL CAS_SERVER_URL CAS_BASE_URL CAS_PUBLIC_URL CAS_CLIENT_ID CAS_CLIENT_SECRET CAS_CALLBACK_URL CAS_CREATE_LOCAL_USERS CAS_ENABLE_SIGNATURE_VALIDATION; do \
             v=$(printenv "$k" 2>/dev/null || true); \
             [ -n "$v" ] && { grep -q "^$k=" .env && sed -i "s#^$k=.*#$k=$v#" .env || echo "$k=$v" >> .env; }; \
           done; \
           php artisan key:generate --force >/dev/null 2>&1 || true; \
           php artisan config:clear >/dev/null 2>&1 || true; \
           php artisan serve --host=0.0.0.0 --port=9101'
