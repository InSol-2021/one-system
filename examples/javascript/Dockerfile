# Minimal image for the One System CAS JavaScript sample.
#
# The SDK dependency ("@cas-system/js-cas-client") is a normal published npm
# package, resolved from the public registry — no local package needed in the
# build context.
#
#     docker build -f examples/javascript/Dockerfile -t cas-sample-js .
#
#   (Run from the `one-system/` directory so the example is inside the build
#    context.)
FROM node:18-alpine

WORKDIR /app

# better-sqlite3 is a native addon — alpine needs a build toolchain to compile it.
RUN apk add --no-cache python3 make g++

# Install example dependencies (resolves @cas-system/js-cas-client from npm,
# and compiles better-sqlite3 for the local username/password store).
COPY examples/javascript/package.json ./examples/javascript/package.json
WORKDIR /app/examples/javascript
RUN npm install --omit=dev

# App source.
COPY examples/javascript/server.js ./server.js
COPY examples/javascript/db.js ./db.js
COPY examples/javascript/public ./public

# Writable directory for the SQLite user store (./data/app.db at runtime).
RUN mkdir -p ./data
ENV APP_DB_PATH=/app/examples/javascript/data/app.db

ENV PORT=9103
EXPOSE 9103

CMD ["node", "server.js"]
