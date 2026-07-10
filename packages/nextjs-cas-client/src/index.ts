/**
 * @module @cas-system/nextjs-cas-client
 * @description Public entry point for the Next.js CAS Client SDK.
 *
 * Re-exports the client-side provider, hooks, components, and shared types.
 * Server-only helpers live under `@cas-system/nextjs-cas-client/server`,
 * route handlers under `/handlers`, and middleware under `/middleware`.
 */

// Provider + context hook
export { CasProvider, useCasAuthContext } from './CasProvider.js';
export type { CasProviderProps } from './CasProvider.js';

// Hooks
export { useCasAuth } from './hooks/useCasAuth.js';
export { useCasUser } from './hooks/useCasUser.js';
export type { UseCasUserReturn } from './hooks/useCasUser.js';

// Components
export { CasLoginButton } from './components/CasLoginButton.js';
export type { CasLoginButtonProps } from './components/CasLoginButton.js';
export { CasProtectedRoute } from './components/CasProtectedRoute.js';
export type { CasProtectedRouteProps } from './components/CasProtectedRoute.js';

// Shared types
export type {
  CasConfig,
  CasServerConfig,
  CasUser,
  CasSessionData,
  CasMiddlewareConfig,
  CasAuthContext,
  CasHandlerConfig,
} from './types.js';
