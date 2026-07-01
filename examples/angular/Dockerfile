# ── Stage 1: build the Angular SPA ──────────────────────────────────────────
# We need the LOCAL SDK package on disk during the build because tsconfig.json
# maps "@one-system/angular-cas-client" to ../../packages/angular-cas-client/src.
# The build context must therefore be the repo's `one-system/` root (see README
# / docker run notes), so both examples/angular and packages/ are available.
FROM node:20-alpine AS build
WORKDIR /build

# Copy the local SDK package first (path referenced by the example).
COPY packages/angular-cas-client ./packages/angular-cas-client

# Copy the example app.
WORKDIR /build/examples/angular
COPY examples/angular/package.json ./package.json
# package.json now lists better-sqlite3 (a native addon used by the backend's
# local account store). Add the build toolchain so `npm install` can compile it
# during the build stage too.
RUN apk add --no-cache python3 make g++ \
  && npm install

COPY examples/angular/ ./
RUN npm run build

# ── Stage 2: tiny runtime (Express + built dist) ─────────────────────────────
FROM node:20-alpine AS runtime
WORKDIR /app
ENV NODE_ENV=production

# Runtime deps: Express + dotenv for the backend, and better-sqlite3 for the
# LOCAL username/password account store (server/db.js). better-sqlite3 is a
# native addon; on alpine (musl) the prebuilt binary may not match, so we add
# the build toolchain just long enough to compile it, then remove it to keep
# the image small.
RUN apk add --no-cache --virtual .build-deps python3 make g++ \
  && npm install --no-package-lock --omit=dev \
       express@^4.19.2 dotenv@^16.4.5 better-sqlite3@^11.3.0 \
  && apk del .build-deps

# Bring over the built SPA and the server (server.js + db.js).
COPY --from=build /build/examples/angular/dist ./dist
COPY examples/angular/server ./server

# Writable data dir for the SQLite file (./data/app.db). The backend also
# creates this at startup, but pre-creating it documents the writable mount
# point and keeps ownership correct for the default node user.
RUN mkdir -p /app/data

ENV APP_PORT=9110
# Location of the local SQLite user store (writable at runtime).
ENV APP_DB_PATH=/app/data/app.db
EXPOSE 9110

CMD ["node", "server/server.js"]
