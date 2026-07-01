# ---------------------------------------------------------------------------
# Dockerfile for the One System CAS .NET sample.
#
# IMPORTANT: the app uses a ProjectReference to the LOCAL package at
#   ../../packages/dotnet-cas-client/CasSystem.Client.csproj
# so the build needs BOTH directories in the build context. Build with the
# repo root ("one-system/") as context:
#
#   docker build -f examples/dotnet/Dockerfile -t cas-dotnet-sample .
#   docker run --rm -p 9106:9106 --env-file examples/dotnet/.env cas-dotnet-sample
#
# (Run these from the one-system/ directory.)
# ---------------------------------------------------------------------------

# ---- Build stage ----
FROM mcr.microsoft.com/dotnet/sdk:8.0-alpine AS build
WORKDIR /src

# Copy the local package and the sample (paths relative to the repo-root context).
COPY packages/dotnet-cas-client/ ./packages/dotnet-cas-client/
COPY examples/dotnet/ ./examples/dotnet/

WORKDIR /src/examples/dotnet
RUN dotnet restore CasDemo.csproj
RUN dotnet publish CasDemo.csproj -c Release -o /app /p:UseAppHost=false

# ---- Runtime stage (small ASP.NET runtime, alpine) ----
FROM mcr.microsoft.com/dotnet/aspnet:8.0-alpine AS runtime
WORKDIR /app
COPY --from=build /app ./

# Writable directory for the local-accounts SQLite file (users table is created
# and the demo users are seeded here on startup). APP_DB_PATH points the app at
# it; mount a volume here to persist accounts across container restarts.
RUN mkdir -p /app/data
ENV APP_DB_PATH=/app/data/app.db
VOLUME ["/app/data"]

ENV PORT=9106
ENV ASPNETCORE_URLS=http://0.0.0.0:9106
EXPOSE 9106

ENTRYPOINT ["dotnet", "CasDemo.dll"]
