import { fileURLToPath } from 'node:url';
import path from 'node:path';
import { createRequire } from 'node:module';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const require = createRequire(import.meta.url);

/**
 * Workaround for a real bug in the PUBLISHED npm package
 * @cas-system/nextjs-cas-client@1.0.0 (verified against the installed
 * node_modules copy, not monorepo source): its package.json "exports" map
 * advertises an ESM build for every subpath (`"import": "./dist/*.mjs"`),
 * but the package's own build step never emits those .mjs files — only the
 * "require"-mapped "./dist/*.js" files exist on disk (which, despite the
 * .js extension, contain ESM `export` syntax that bundlers like webpack
 * still parse fine).
 *
 * Because Next's webpack resolver picks the "import" condition first and
 * that target file is missing, resolution fails outright ("Module not
 * found") for every entry point. Point each subpath at the file that
 * ACTUALLY exists in the installed package instead. This is resolved via
 * Node's own module resolution (require.resolve), so it always points at
 * whatever is really inside node_modules/@cas-system/nextjs-cas-client —
 * never at monorepo source — and should be removed once the package fixes
 * its exports map / ships the missing .mjs files.
 */
const casPkgDist = path.dirname(require.resolve('@cas-system/nextjs-cas-client'));

/** @type {import('next').NextConfig} */
const nextConfig = {
  // Produce a self-contained server bundle so the Docker image stays small.
  output: 'standalone',

  // Pin the trace root to this app dir so the standalone entrypoint is always
  // `.next/standalone/server.js` regardless of where npm hoists dependencies.
  outputFileTracingRoot: __dirname,

  // better-sqlite3 is a NATIVE module (ships a compiled .node binary). It must
  // not be bundled by webpack — keep it external so Next requires it at runtime
  // from node_modules. The standalone output then traces the native binary in.
  experimental: {
    serverComponentsExternalPackages: ['better-sqlite3'],
  },

  webpack(config) {
    config.resolve.alias = {
      ...config.resolve.alias,
      '@cas-system/nextjs-cas-client/handlers': path.join(casPkgDist, 'handlers/index.js'),
      '@cas-system/nextjs-cas-client/middleware': path.join(casPkgDist, 'middleware.js'),
      '@cas-system/nextjs-cas-client/server': path.join(casPkgDist, 'server/index.js'),
      '@cas-system/nextjs-cas-client$': path.join(casPkgDist, 'index.js'),
    };
    return config;
  },
};

export default nextConfig;
