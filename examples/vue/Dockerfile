# ---------------------------------------------------------------------------
# Dockerfile for the Vue CAS client sample.
#
# IMPORTANT: build with the repo's `one-system/` directory as the build
# CONTEXT, because this sample depends on a sibling package via
# `file:../../packages/vue-cas-client`, which lives OUTSIDE this folder:
#
#   docker build -f examples/vue/Dockerfile -t vue-cas-sample .
#   docker run --rm -p 9109:9109 --env-file examples/vue/.env vue-cas-sample
#
# (Run both commands from the `one-system/` directory.)
# ---------------------------------------------------------------------------

# --- Build stage: install deps + build the SPA -----------------------------
FROM node:20-alpine AS build
WORKDIR /app

# Build toolchain for native addons (better-sqlite3 compiles from source).
RUN apk add --no-cache python3 make g++

# Copy the local package first (the file: dependency target).
COPY packages/vue-cas-client ./packages/vue-cas-client

# Copy the sample's manifests and install (resolves the file: link).
COPY examples/vue/package.json ./examples/vue/package.json
WORKDIR /app/examples/vue
RUN npm install

# Copy the rest of the sample and build the production bundle.
COPY examples/vue ./
RUN npm run build

# --- Runtime stage: small image that serves dist/ + the API ----------------
FROM node:20-alpine AS runtime
WORKDIR /app/examples/vue
ENV NODE_ENV=production

# Bring over installed node_modules (incl. the compiled better-sqlite3 binary),
# build output, server, and manifest.
COPY --from=build /app/examples/vue/node_modules ./node_modules
COPY --from=build /app/examples/vue/dist ./dist
COPY --from=build /app/examples/vue/server ./server
COPY --from=build /app/examples/vue/package.json ./package.json

# Writable directory for the local SQLite user store (server/db.js writes here).
# Owned by the unprivileged `node` user so the DB file is writable at runtime.
RUN mkdir -p /app/examples/vue/data && chown -R node:node /app/examples/vue/data
USER node

EXPOSE 9109
CMD ["node", "server/index.js"]
