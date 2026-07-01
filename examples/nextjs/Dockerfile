# syntax=docker/dockerfile:1
#
# Minimal multi-stage image for the Next.js CAS sample.
#
# IMPORTANT: this sample depends on the LOCAL package at
#   ../../packages/nextjs-cas-client
# via "file:../../packages/nextjs-cas-client". So the Docker BUILD CONTEXT must
# include both the example and the package. Build from the repo's one-system/ root:
#
#   docker build -f examples/nextjs/Dockerfile -t cas-nextjs-sample one-system/
#   # (run from the directory that CONTAINS one-system/, or adjust paths)
#
# ...or from inside one-system/:
#   docker build -f examples/nextjs/Dockerfile -t cas-nextjs-sample .
#
# Then run (pass your env, mapping container 9108 to host 9108):
#   docker run --rm -p 9108:9108 --env-file examples/nextjs/.env.local cas-nextjs-sample

# ---- deps + build -----------------------------------------------------------
FROM node:22-alpine AS builder
WORKDIR /build

# better-sqlite3 is a native addon — compiling it from source during
# `npm install` needs python3 + a C/C++ toolchain on Alpine.
RUN apk add --no-cache python3 make g++

# Copy the local package first (the example's file: dependency points at it).
COPY packages/nextjs-cas-client ./packages/nextjs-cas-client

# Copy the example app.
COPY examples/nextjs ./examples/nextjs

WORKDIR /build/examples/nextjs
RUN npm install

# CAS config needed at BUILD time: Next collects route-handler page data, which
# instantiates the CAS client (it throws if serverUrl is missing). NEXT_PUBLIC_*
# values are also baked into the client bundle here. (Deploy-specific values — for
# a portable image, pass these as build ARGs instead of hardcoding.)
#
# SPLIT-HORIZON: the browser reaches CAS at the PUBLIC url (CAS_PUBLIC_URL /
# NEXT_PUBLIC_CAS_PUBLIC_URL), while this container reaches CAS for back-channel
# token validation at the INTERNAL url (CAS_SERVER_URL = the docker service name).
ENV CAS_SERVER_URL=http://one-system-cas \
    CAS_PUBLIC_URL=http://161.97.158.202:91 \
    CAS_CLIENT_ID=nextjs-sample \
    CAS_CLIENT_SECRET=demo-secret-nextjs \
    CAS_CALLBACK_URL=http://161.97.158.202:9108/api/cas/callback \
    CAS_COOKIE_SECRET=demo-nextjs-cookie-secret-change-me-0123456789 \
    NEXT_PUBLIC_CAS_PUBLIC_URL=http://161.97.158.202:91 \
    NEXT_PUBLIC_CAS_CLIENT_ID=nextjs-sample \
    NEXT_PUBLIC_CAS_CALLBACK_URL=http://161.97.158.202:9108/api/cas/callback
RUN npm run build

# ---- runtime ----------------------------------------------------------------
FROM node:22-alpine AS runner
WORKDIR /app
ENV NODE_ENV=production
ENV PORT=9108
ENV HOSTNAME=0.0.0.0
# Server-side CAS config read at runtime by the Next route handlers.
# CAS_SERVER_URL is the INTERNAL back-channel url (server-to-server token
# validation). CAS_PUBLIC_URL is the PUBLIC url the package middleware uses to
# build the browser /sso/login redirect.
ENV CAS_SERVER_URL=http://one-system-cas \
    CAS_PUBLIC_URL=http://161.97.158.202:91 \
    CAS_CLIENT_ID=nextjs-sample \
    CAS_CLIENT_SECRET=demo-secret-nextjs \
    CAS_CALLBACK_URL=http://161.97.158.202:9108/api/cas/callback \
    CAS_COOKIE_SECRET=demo-nextjs-cookie-secret-change-me-0123456789

# Local user store (SQLite). The DB file is created on first startup at this
# path, which lives in a writable directory we provision below. Mount a volume
# here to persist accounts across container restarts.
ENV APP_DB_PATH=/app/data/app.db

# libstdc++ is needed by the better-sqlite3 native addon at runtime.
RUN apk add --no-cache libstdc++

# Next.js "standalone" output bundles only what is needed to run.
# outputFileTracingRoot is pinned to the app dir, so the layout is flat:
#   .next/standalone/server.js  +  .next/standalone/.next/static
# The standalone trace also brings in better-sqlite3 (incl. its native .node).
COPY --from=builder /build/examples/nextjs/.next/standalone ./
COPY --from=builder /build/examples/nextjs/.next/static ./.next/static
COPY --from=builder /build/examples/nextjs/public ./public

# Writable data dir for the SQLite file. `node` is the non-root user that the
# official node:alpine image ships; make sure it can write the DB at runtime.
RUN mkdir -p /app/data && chown -R node:node /app/data
USER node
VOLUME ["/app/data"]

EXPOSE 9108
CMD ["node", "server.js"]
