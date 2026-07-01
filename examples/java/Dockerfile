# ----------------------------------------------------------------------------
# One System CAS - Java (Spring Boot) sample - minimal multi-stage image.
#
# IMPORTANT: build context must be the repo's `one-system/` root, NOT this
# folder, because the build needs the local java-cas-client package source.
# From one-system/:
#
#   docker build -f examples/java/Dockerfile -t one-system-cas-java .
#   docker run --rm -p 9105:9105 --env-file examples/java/.env one-system-cas-java
# ----------------------------------------------------------------------------

# ---- Stage 1: build ----
FROM maven:3.9-eclipse-temurin-17 AS build
WORKDIR /build

# 1) Install the LOCAL CAS client package into the build's Maven repo.
#    (no published artifact -> we install com.cassystem:cas-client:2.0.0 here)
COPY packages/java-cas-client /build/java-cas-client
RUN mvn -q -f /build/java-cas-client/pom.xml install -DskipTests

# 2) Build the sample fat jar against that locally-installed package.
COPY examples/java/pom.xml /build/sample/pom.xml
COPY examples/java/src /build/sample/src
RUN mvn -q -f /build/sample/pom.xml package -DskipTests

# ---- Stage 2: runtime (small JRE-only image) ----
FROM eclipse-temurin:17-jre AS runtime
WORKDIR /app
COPY --from=build /build/sample/target/cas-java-sample.jar /app/app.jar

# Writable data dir for the LOCAL SQLite user store.
# application.properties points at jdbc:sqlite:./data/app.db (relative to /app).
# Create it and hand ownership to a non-root user so the file is writable at
# runtime (SQLite must be able to create/modify ./data/app.db).
RUN mkdir -p /app/data \
    && useradd --system --uid 10001 --home-dir /app appuser \
    && chown -R appuser:appuser /app
USER appuser

EXPOSE 9105
ENTRYPOINT ["java", "-jar", "/app/app.jar"]
