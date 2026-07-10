import { fileURLToPath } from 'node:url';
import path from 'node:path';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

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
};

export default nextConfig;
