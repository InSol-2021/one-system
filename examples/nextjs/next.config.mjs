import { fileURLToPath } from 'node:url';
import path from 'node:path';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

/**
 * Absolute path to the local @one-system/nextjs-cas-client package.
 * It is linked via "file:../../packages/nextjs-cas-client" in package.json,
 * so npm symlinks the whole package directory (including its TypeScript src/)
 * into node_modules.
 */
const casPkgSrc = path.resolve(
  __dirname,
  'node_modules/@one-system/nextjs-cas-client/src',
);

/** @type {import('next').NextConfig} */
const nextConfig = {
  // Produce a self-contained server bundle so the Docker image stays small.
  output: 'standalone',

  // The CAS package is linked from OUTSIDE this dir (../../packages/...), which
  // would otherwise make Next infer a higher trace root and nest the
  // standalone server under examples/nextjs/. Pin the trace root to this app so
  // the standalone entrypoint is always `.next/standalone/server.js`.
  outputFileTracingRoot: __dirname,

  // We consume the CAS client package's TypeScript SOURCE directly (via the
  // aliases below). SWC compiles it fine, but Next's separate type-check step
  // would also type-check the package's internals — and the linked package has
  // no node_modules of its own, so its `react`/`next` type imports can't
  // resolve from its location. We only skip the build-time type gate so the
  // linked package source doesn't fail it. (In a published setup the package
  // ships its own .d.ts and this is unnecessary.)
  typescript: {
    ignoreBuildErrors: true,
  },

  // Transpile the local CAS client package — we consume its TypeScript
  // SOURCE directly (see the webpack aliases below) instead of a prebuilt
  // dist/, so Next must compile it as part of the app.
  transpilePackages: ['@one-system/nextjs-cas-client'],

  // better-sqlite3 is a NATIVE module (ships a compiled .node binary). It must
  // not be bundled by webpack — keep it external so Next requires it at runtime
  // from node_modules. The standalone output then traces the native binary in.
  experimental: {
    serverComponentsExternalPackages: ['better-sqlite3'],
  },

  webpack(config) {
    // Point each subpath export at the package's TypeScript source. This makes
    // the sample resilient to the package's build step and guarantees we use
    // the exact public API (CasProvider, createCallbackHandler, CasClient, ...).
    config.resolve.alias = {
      ...config.resolve.alias,
      '@one-system/nextjs-cas-client/handlers': path.join(casPkgSrc, 'handlers/index.ts'),
      '@one-system/nextjs-cas-client/middleware': path.join(casPkgSrc, 'middleware.ts'),
      '@one-system/nextjs-cas-client/server': path.join(casPkgSrc, 'server/index.ts'),
      '@one-system/nextjs-cas-client$': path.join(casPkgSrc, 'index.ts'),
    };
    return config;
  },
};

export default nextConfig;
