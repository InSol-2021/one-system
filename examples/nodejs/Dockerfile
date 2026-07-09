# Minimal image for the One System CAS Node.js sample.
# Small base; production-only deps.
FROM node:20-alpine

WORKDIR /app

# better-sqlite3 is a native addon: it is compiled from source during npm install,
# which needs python3 + a C/C++ toolchain on Alpine. Install them only for the build.
RUN apk add --no-cache --virtual .build-deps python3 make g++

# The sample depends on the published "@cas-system/node-cas-client" package from
# the npm registry (see package.json). No monorepo source needs to be present in
# the build context for this step.
COPY examples/nodejs/package.json /app/package.json

# Install only production dependencies (this compiles better-sqlite3).
RUN npm install --omit=dev \
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
