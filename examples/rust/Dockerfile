# Multi-stage image for the One System CAS Rust / axum sample.
#
# IMPORTANT — build context:
# This sample depends on the LOCAL crate at packages/rust-cas-client (a path
# dependency, OUTSIDE this directory). So the Docker build context MUST be the
# repo's "one-system" root (the dir containing both packages/ and examples/),
# and you point at this Dockerfile with -f. From the one-system root:
#
#   docker build -f examples/rust/Dockerfile -t one-system-sample-rust .
#   docker run --rm -p 9111:9111 --env-file examples/rust/.env one-system-sample-rust
#
# ---------------------------------------------------------------------------
# Stage 1 — build a static-ish release binary.
# ---------------------------------------------------------------------------
FROM rust:1-slim AS builder

WORKDIR /build

# Copy the local crate and the sample so the path dependency resolves.
COPY packages/rust-cas-client/ /build/packages/rust-cas-client/
COPY examples/rust/ /build/examples/rust/

# Build the sample (release). rusqlite is built with the "bundled" feature, so
# no system libsqlite3 is required. reqwest uses rustls (no OpenSSL needed).
WORKDIR /build/examples/rust
RUN cargo build --release \
    && cp target/release/rust-sample /usr/local/bin/rust-sample

# ---------------------------------------------------------------------------
# Stage 2 — slim runtime.
# ---------------------------------------------------------------------------
FROM debian:bookworm-slim AS runtime

# ca-certificates so the rustls HTTPS client can verify the CAS server.
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# A writable data dir for the SQLite file (./data/app.db at runtime).
RUN mkdir -p /app/data
ENV APP_DB_PATH=/app/data/app.db

COPY --from=builder /usr/local/bin/rust-sample /usr/local/bin/rust-sample

ENV PORT=9111
EXPOSE 9111

CMD ["rust-sample"]
