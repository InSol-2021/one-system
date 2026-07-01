import { InjectionToken } from '@angular/core';

/**
 * Configuration options for the CAS client SDK.
 *
 * @remarks
 * Provide this via `CasModule.forRoot(config)` or the `CAS_CONFIG` injection token.
 *
 * @example
 * ```typescript
 * const casConfig: CasConfig = {
 *   serverUrl: 'https://cas.example.com',
 *   clientId: 'my-app',
 *   callbackUrl: 'https://my-app.com/cas/callback',
 *   backendValidateUrl: '/api/auth/validate',
 * };
 * ```
 */
export interface CasConfig {
  /**
   * Base URL of the CAS server (no trailing slash).
   *
   * @example `'https://cas.example.com'`
   */
  serverUrl: string;

  /**
   * The OAuth / SSO client identifier registered with the CAS server.
   */
  clientId: string;

  /**
   * URL to which the CAS server redirects after successful login.
   * Defaults to `window.location.origin + '/cas/callback'` when omitted.
   */
  callbackUrl?: string;

  /**
   * Your backend endpoint that proxies token validation to the CAS server.
   * When provided, `validateTokenViaBackend()` will POST the token here
   * instead of calling the CAS server directly from the browser.
   *
   * @example `'/api/auth/validate'`
   */
  backendValidateUrl?: string;
}

/**
 * Represents an authenticated user returned by the CAS server.
 *
 * @example
 * ```typescript
 * const user: CasUser = {
 *   id: '42',
 *   username: 'jdoe',
 *   email: 'jdoe@example.com',
 *   roles: ['admin', 'editor'],
 * };
 * ```
 */
export interface CasUser {
  /** Unique identifier of the user. */
  id: string;

  /** Login username. */
  username: string;

  /** Email address. */
  email: string;

  /**
   * Roles assigned to the user (e.g. `['admin', 'editor']`).
   * May be `undefined` if the CAS server does not return roles.
   */
  roles?: string[];
}

/**
 * Envelope returned by the CAS server's token-validation endpoint
 * (`POST {serverUrl}/api/sso/validate` or `/api/validate-token`).
 *
 * @example
 * ```typescript
 * const response: CasValidateResponse = {
 *   valid: true,
 *   user: { id: '42', username: 'jdoe', email: 'jdoe@example.com' },
 *   expires_at: '2026-01-01T00:00:00Z',
 * };
 * ```
 */
export interface CasValidateResponse {
  /** `true` when the token validated successfully. */
  valid: boolean;

  /** The authenticated user (present only when `valid` is `true`). */
  user?: CasUser;

  /** ISO datetime at which the token expires. */
  expires_at?: string;
}

/**
 * Angular injection token used to provide `CasConfig` at the module level.
 *
 * @example
 * ```typescript
 * providers: [{ provide: CAS_CONFIG, useValue: casConfig }]
 * ```
 */
export const CAS_CONFIG = new InjectionToken<CasConfig>('CAS_CONFIG');
