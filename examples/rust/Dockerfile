# Multi-stage image for the One System CAS Rust / axum sample.
#
# The sample depends on the published `rust-cas-client` crate from crates.io
# (a normal registry dependency — see Cargo.toml), so the build only needs
# this sample's own sources. The Docker build context is still the repo's
# "one-system" root (kept consistent with the other examples / compose file),
# so you still point at this Dockerfile with -f:
#
#   docker build -f examples/rust/Dockerfile -t one-system-sample-rust .
#   docker run --rm -p 9111:9111 --env-file examples/rust/.env one-system-sample-rust
#
# ---------------------------------------------------------------------------
# Stage 1 — build a static-ish release binary.
# ---------------------------------------------------------------------------
FROM rust:1-slim AS builder

WORKDIR /build

# Copy the sample sources. rust-cas-client is fetched from crates.io by cargo.
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
