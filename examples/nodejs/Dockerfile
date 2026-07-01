# Minimal image for the One System CAS Node.js sample.
# Small base; production-only deps.
FROM node:20-alpine

WORKDIR /app

# better-sqlite3 is a native addon: it is compiled from source during npm install,
# which needs python3 + a C/C++ toolchain on Alpine. Install them only for the build.
RUN apk add --no-cache --virtual .build-deps python3 make g++

# The sample depends on the LOCAL package via "file:../../packages/nodejs-cas-client".
# Copy the package source first so the file: dependency can be resolved at install time.
# Build context must be the repo root (one-system/) — see README "Run with Docker".
COPY packages/nodejs-cas-client/ /packages/nodejs-cas-client/
COPY examples/nodejs/package.json /app/package.json

# Rewrite the relative path to the location the package was copied to inside the image,
# then install only production dependencies (this compiles better-sqlite3).
RUN sed -i 's#file:../../packages/nodejs-cas-client#file:/packages/nodejs-cas-client#' package.json \
    && (cd /packages/nodejs-cas-client && npm install --omit=dev) \
    && npm install --omit=dev \
    && apk del .build-deps

# Copy the rest of the sample.
COPY examples/nodejs/ /app/

# The SQLite user store lives here and must be writable at runtime.
# DB_PATH points the app at this directory (see db.js).
RUN mkdir -p /app/data
ENV DB_PATH=/app/data/app.db

ENV PORT=9102
EXPOSE 9102

CMD ["node", "server.js"]
