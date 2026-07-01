/**
 * Liveness probe — GET /api/health.
 * Public (listed in middleware `publicPaths`) so it works without auth,
 * handy for the unified single-server deployment.
 */
import { NextResponse } from 'next/server';

export function GET() {
  return NextResponse.json({ status: 'ok' });
}
