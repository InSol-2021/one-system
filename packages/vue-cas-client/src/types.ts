import type { InjectionKey } from 'vue';

// ---------------------------------------------------------------------------
// Configuration
// ---------------------------------------------------------------------------

/**
 * Configuration object for the CAS client.
 *
 * @example
 * ```ts
 * const config: CasConfig = {
 *   serverUrl: 'https://cas.example.com',
 *   clientId: 'my-app',
 *   callbackUrl: 'https://my-app.com/auth/callback',
 *   backendValidateUrl: '/api/auth/validate',
 * };
 * ```
 */
export interface CasConfig {
  /**
   * Base URL of the CAS server (no trailing slash).
   *
   * @example 'https://cas.example.com'
   */
  serverUrl: string;

  /**
   * The `client_id` registered on the CAS server for this application.
   */
  clientId: string;

  /**
   * URL the CAS server redirects to after successful login.
   * Defaults to `window.location.origin + '/auth/callback'` when omitted.
   */
  callbackUrl?: string;

  /**
   * URL of your **backend** endpoint that proxies token validation to the
   * CAS server. Tokens must **never** be validated in the browser because
   * that would expose the `client_secret`.
   *
   * Your backend should accept `POST { token }` and forward it to
   * `{serverUrl}/api/validate-token` along with `client_id` and
   * `client_secret`.
   *
   * @example '/api/auth/validate'
   */
  backendValidateUrl?: string;
}

// ---------------------------------------------------------------------------
// User
// ---------------------------------------------------------------------------

/**
 * Authenticated CAS user object.
 */
export interface CasUser {
  /** Unique user identifier. */
  id: string | number;

  /** Username (login handle). */
  username: string;

  /** E-mail address. */
  email: string;

  /**
   * Roles assigned to the user.
   *
   * @example ['admin', 'editor']
   */
  roles?: string[];
}

// ---------------------------------------------------------------------------
// Auth State
// ---------------------------------------------------------------------------

/**
 * Reactive authentication state exposed by the plugin and composables.
 */
export interface CasAuthState {
  /** The currently authenticated user, or `null`. */
  user: CasUser | null;

  /** `true` when a valid user session exists. */
  isAuthenticated: boolean;

  /** `true` while an async auth operation is in-flight. */
  isLoading: boolean;

  /** The last error message, or `null`. */
  error: string | null;
}

// ---------------------------------------------------------------------------
// Plugin options
// ---------------------------------------------------------------------------

/**
 * Options accepted by `CasPlugin.install()`.
 */
export interface CasPluginOptions extends CasConfig {
  /**
   * When `true`, the plugin will automatically call `handleCallback()` on
   * install if the current URL contains a `token` query parameter.
   *
   * @default true
   */
  autoHandleCallback?: boolean;
}

// ---------------------------------------------------------------------------
// Navigation-guard options
// ---------------------------------------------------------------------------

/**
 * Options for the Vue Router navigation guard factory.
 */
export interface CasGuardOptions {
  /**
   * Override the CAS login URL used for unauthenticated redirects.
   * When omitted the guard uses the URL produced by `CasClient.getLoginUrl()`.
   */
  loginUrl?: string;

  /**
   * Global roles required to access *any* guarded route.
   * Per-route roles can be specified via `meta.roles`.
   */
  roles?: string[];

  /**
   * When `true` (default), unauthenticated users are redirected to the CAS
   * login page. When `false`, the guard simply rejects navigation.
   *
   * @default true
   */
  redirectToLogin?: boolean;
}

// ---------------------------------------------------------------------------
// Injection key
// ---------------------------------------------------------------------------

/**
 * Vue injection key used by the CAS plugin and composables to share the
 * reactive auth context across the component tree.
 */
export interface CasAuthContext {
  /** The underlying CAS client instance. */
  client: import('./cas-client').CasClient;

  /** Reactive auth state. */
  state: import('vue').Reactive<CasAuthState>;
}

/**
 * Symbol-based injection key for the CAS auth context.
 *
 * @example
 * ```ts
 * // Inside a component
 * const ctx = inject(CAS_AUTH_KEY);
 * ```
 */
export const CAS_AUTH_KEY: InjectionKey<CasAuthContext> = Symbol('cas-auth');
