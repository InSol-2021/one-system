/**
 * @module types
 * @description Core TypeScript interfaces for the CAS Client SDK.
 */

/**
 * Configuration for the CAS client.
 *
 * @example
 * ```ts
 * const config: CasConfig = {
 *   serverUrl: 'https://cas.example.com',
 *   clientId: 'my-app',
 *   callbackUrl: 'https://myapp.com/auth/callback',
 *   backendValidateUrl: '/api/auth/validate',
 * };
 * ```
 */
export interface CasConfig {
  /**
   * The base URL of the CAS server (no trailing slash).
   * @example 'https://cas.example.com'
   */
  serverUrl: string;

  /**
   * The OAuth client ID registered with the CAS server.
   */
  clientId: string;

  /**
   * The URL the CAS server should redirect to after authentication.
   * Defaults to the current page URL (`window.location.href`) if omitted.
   */
  callbackUrl?: string;

  /**
   * URL of your backend endpoint that proxies token validation to the CAS server.
   * Tokens are NEVER validated in the browser — they must be sent to your backend,
   * which calls `POST {serverUrl}/api/validate-token` with the `client_secret`.
   *
   * @example '/api/auth/validate'
   */
  backendValidateUrl?: string;
}

/**
 * Represents an authenticated CAS user.
 */
export interface CasUser {
  /** Unique user identifier from the CAS server. */
  id: string;

  /** The user's login name. */
  username: string;

  /** The user's email address. */
  email: string;

  /**
   * Roles assigned to this user (e.g. `['admin', 'editor']`).
   * May be `undefined` if the CAS server does not return roles.
   */
  roles?: string[];
}

/**
 * The authentication state exposed by the CAS context and hooks.
 */
export interface CasAuthState {
  /** The currently authenticated user, or `null` if not authenticated. */
  user: CasUser | null;

  /** Whether the user is currently authenticated. */
  isAuthenticated: boolean;

  /** Whether the SDK is performing an authentication operation (e.g. validating a callback token). */
  isLoading: boolean;

  /** The last authentication error, or `null` if no error occurred. */
  error: string | null;
}

/**
 * Actions available through the CAS context.
 */
export interface CasAuthActions {
  /**
   * Redirect the user to the CAS SSO login page.
   * @param returnUrl - Optional URL to return to after login (defaults to current page).
   */
  login: (returnUrl?: string) => void;

  /**
   * Log the user out: clears the session, calls the CAS logout endpoint, and optionally redirects.
   * @param redirectUrl - Optional URL to redirect to after logout.
   */
  logout: (redirectUrl?: string) => Promise<void>;

  /**
   * Check whether the current user has a specific role.
   * @param role - The role to check.
   * @returns `true` if the user has the given role.
   */
  hasRole: (role: string) => boolean;

  /**
   * Check whether the current user has at least one of the given roles.
   * @param roles - The roles to check.
   * @returns `true` if the user has any of the given roles.
   */
  hasAnyRole: (roles: string[]) => boolean;
}

/**
 * The full context value provided by {@link CasProvider}.
 */
export interface CasContextValue extends CasAuthState, CasAuthActions {}
