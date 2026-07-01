# ---------------------------------------------------------------------------
# Minimal image for the React CAS sample.
#
# In production a SINGLE Node/Express process (server.js) serves both the built
# SPA (./dist) and the /api endpoints, so there is no separate web server.
#
# Build context note: this sample depends on the LOCAL package via
# "file:../../packages/react-cas-client", so the image MUST be built with the
# monorepo root (the directory that contains both packages/ and examples/) as
# the build context. All COPY paths below are relative to that root.
#
# From this directory (examples/react):
#
#     docker build -f Dockerfile -t cas-react-sample ../..
#
# ...where ../.. is the monorepo root used as the build context.
# ---------------------------------------------------------------------------
FROM node:20-alpine AS build
WORKDIR /app

# Bring in the local package the sample links to (relative "file:" dependency).
COPY packages/react-cas-client /packages/react-cas-client

# Install deps + build the SPA.
COPY examples/react/package.json ./package.json
RUN npm install
COPY examples/react/ ./
RUN npm run build

# ---------------------------------------------------------------------------
# Runtime image: only what's needed to run Express + serve ./dist.
# ---------------------------------------------------------------------------
FROM node:20-alpine AS runtime
WORKDIR /app
ENV NODE_ENV=production

# Install ONLY the runtime deps. We deliberately do not reuse the sample's
# package.json here, because it declares the local "file:" SDK dependency that
# is irrelevant at runtime (the SDK was already compiled into ./dist by Vite).
#
# better-sqlite3 is a NATIVE module, so building it from source on Alpine needs
# python3/make/g++. We install those toolchain packages only to compile it, then
# remove them in the same layer to keep the runtime image small.
RUN apk add --no-cache --virtual .build-deps python3 make g++ \
 && npm init -y >/dev/null \
 && npm install --omit=dev \
      express@^4.19.2 \
      express-session@^1.18.1 \
      better-sqlite3@^11.8.1 \
      dotenv@^16.4.5 \
 && npm pkg set type=module \
 && npm cache clean --force \
 && apk del .build-deps

# Copy server + built SPA from the build stage.
COPY examples/react/server.js ./server.js
COPY --from=build /app/dist ./dist

# The local SQLite user store lives here and MUST be writable at runtime.
# Default DB_PATH (server.js) is ./data/app.db; create it and hand it to the
# non-root "node" user that the base image ships with.
RUN mkdir -p /app/data && chown -R node:node /app
USER node

EXPOSE 9107
ENV PORT=9107
# Where the SQLite file is created/opened at runtime (must be writable).
ENV DB_PATH=/app/data/app.db
CMD ["node", "server.js"]
