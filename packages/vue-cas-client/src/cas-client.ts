import type { CasConfig, CasUser } from './types';

/** Key used to persist the JWT in sessionStorage. */
const TOKEN_STORAGE_KEY = 'cas_token';

/** Key used to persist the serialised user in sessionStorage. */
const USER_STORAGE_KEY = 'cas_user';

/**
 * Browser-side CAS client.
 *
 * Handles the SSO redirect flow, token extraction, backend-delegated
 * validation, user retrieval, role checks, and logout.
 *
 * > **Security note** — Token validation is **never** performed in the
 * > browser. The `validateTokenViaBackend()` method delegates to your own
 * > backend, which has access to the `client_secret`.
 *
 * @example
 * ```ts
 * const client = new CasClient({
 *   serverUrl: 'https://cas.example.com',
 *   clientId: 'my-app',
 *   callbackUrl: 'https://my-app.com/auth/callback',
 *   backendValidateUrl: '/api/auth/validate',
 * });
 *
 * // Redirect to CAS login
 * client.login();
 *
 * // On callback page
 * const user = await client.handleCallback();
 * ```
 */
export class CasClient {
  private readonly config: Required<Pick<CasConfig, 'serverUrl' | 'clientId'>> &
    Pick<CasConfig, 'callbackUrl' | 'backendValidateUrl'>;

  /**
   * Create a new CAS client instance.
   *
   * @param config - CAS configuration. See {@link CasConfig}.
   */
  constructor(config: CasConfig) {
    if (!config.serverUrl) {
      throw new Error('[vue-cas-client] "serverUrl" is required.');
    }
    if (!config.clientId) {
      throw new Error('[vue-cas-client] "clientId" is required.');
    }

    this.config = {
      serverUrl: config.serverUrl.replace(/\/+$/, ''),
      clientId: config.clientId,
      callbackUrl: config.callbackUrl,
      backendValidateUrl: config.backendValidateUrl,
    };
  }

  // -----------------------------------------------------------------------
  // Login helpers
  // -----------------------------------------------------------------------

  /**
   * Build the full CAS SSO login URL.
   *
   * Per the CAS protocol the browser is redirected to
   * `GET {serverUrl}/sso/login?client_id={clientId}`. The CAS server uses the
   * callback URL **registered** for this `client_id` and then 302-redirects
   * back to it with the issued token appended as `?token=<jwt>`. The client
   * does not pass a `redirect_uri`/`response_type` on this request.
   *
   * @param _returnUrl - Unused. Kept for backwards-compatible call sites; the
   *   post-login destination is the callback URL registered on the CAS server.
   * @returns The CAS login URL.
   */
  getLoginUrl(_returnUrl?: string): string {
    const params = new URLSearchParams({
      client_id: this.config.clientId,
    });

    return `${this.config.serverUrl}/sso/login?${params.toString()}`;
  }

  /**
   * Redirect the browser to the CAS SSO login page.
   *
   * @param returnUrl - Optional post-login redirect URL.
   */
  login(returnUrl?: string): void {
    window.location.href = this.getLoginUrl(returnUrl);
  }

  // -----------------------------------------------------------------------
  // Token handling
  // -----------------------------------------------------------------------

  /**
   * Extract the JWT `token` from the current page URL query string.
   *
   * The CAS server redirects back to the callback URL with
   * `?token=JWT_TOKEN`. This method reads and returns that value.
   *
   * @returns The JWT string, or `null` if not present.
   */
  extractTokenFromUrl(): string | null {
    const url = new URL(window.location.href);
    return url.searchParams.get('token');
  }

  /**
   * Validate a JWT token by delegating to your **backend** validation
   * endpoint.
   *
   * Your backend should accept `POST { token }` and forward it to the CAS
   * server at `{serverUrl}/api/validate-token` along with the `client_id`
   * and `client_secret` stored server-side.
   *
   * @param token - The JWT string to validate.
   * @returns The validated {@link CasUser} object.
   * @throws If no `backendValidateUrl` is configured or the request fails.
   */
  async validateTokenViaBackend(token: string): Promise<CasUser> {
    const validateUrl = this.config.backendValidateUrl;

    if (!validateUrl) {
      throw new Error(
        '[vue-cas-client] "backendValidateUrl" must be configured to validate tokens. ' +
          'Tokens must never be validated in the browser.',
      );
    }

    const response = await fetch(validateUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ token }),
    });

    if (!response.ok) {
      const body = await response.text().catch(() => '');
      throw new Error(
        `[vue-cas-client] Token validation failed (${response.status}): ${body}`,
      );
    }

    const data = (await response.json()) as { user?: CasUser } & CasUser;

    // Support both `{ user: CasUser }` and flat `CasUser` responses.
    const user: CasUser = data.user ?? data;

    if (!user || !user.id || !user.username) {
      throw new Error(
        '[vue-cas-client] Backend validation returned an invalid user object.',
      );
    }

    return user;
  }

  // -----------------------------------------------------------------------
  // Callback handling
  // -----------------------------------------------------------------------

  /**
   * Handle the CAS callback — extract the token from the URL, validate it
   * via the backend, and persist the session in `sessionStorage`.
   *
   * Call this method on your callback page (e.g. `/auth/callback`).
   *
   * @returns The authenticated {@link CasUser}.
   * @throws If no token is found in the URL or validation fails.
   */
  async handleCallback(): Promise<CasUser> {
    const token = this.extractTokenFromUrl();

    if (!token) {
      throw new Error(
        '[vue-cas-client] No token found in the callback URL.',
      );
    }

    const user = await this.validateTokenViaBackend(token);

    // Persist session
    this.setToken(token);
    this.setUser(user);

    // Clean the URL so the token isn't visible in the address bar / history.
    this.cleanUrl();

    return user;
  }

  // -----------------------------------------------------------------------
  // Session accessors
  // -----------------------------------------------------------------------

  /**
   * Retrieve the currently authenticated user from `sessionStorage`.
   *
   * @returns The {@link CasUser}, or `null` if no session exists.
   */
  getUser(): CasUser | null {
    try {
      const raw = sessionStorage.getItem(USER_STORAGE_KEY);
      return raw ? (JSON.parse(raw) as CasUser) : null;
    } catch {
      return null;
    }
  }

  /**
   * Retrieve the stored JWT token from `sessionStorage`.
   *
   * @returns The JWT string, or `null`.
   */
  getToken(): string | null {
    return sessionStorage.getItem(TOKEN_STORAGE_KEY);
  }

  /**
   * Check whether the user is currently authenticated (i.e. a user object
   * exists in `sessionStorage`).
   */
  isAuthenticated(): boolean {
    return this.getUser() !== null && this.getToken() !== null;
  }

  // -----------------------------------------------------------------------
  // Logout
  // -----------------------------------------------------------------------

  /**
   * Log out the current user.
   *
   * 1. Calls the CAS server's logout endpoint.
   * 2. Clears `sessionStorage`.
   * 3. Optionally redirects to `redirectUrl`.
   *
   * @param redirectUrl - URL to redirect to after logout. Defaults to
   *   `window.location.origin`.
   */
  async logout(redirectUrl?: string): Promise<void> {
    const token = this.getToken();

    // Best-effort server-side logout.
    try {
      await fetch(`${this.config.serverUrl}/api/logout`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
      });
    } catch {
      // Swallow network errors — we still want to clear the local session.
    }

    this.clearSession();

    const target = redirectUrl ?? window.location.origin;
    window.location.href = target;
  }

  // -----------------------------------------------------------------------
  // Role helpers
  // -----------------------------------------------------------------------

  /**
   * Check if the current user has a specific role.
   *
   * @param role - The role to check for.
   */
  userHasRole(role: string): boolean {
    const user = this.getUser();
    return user?.roles?.includes(role) ?? false;
  }

  /**
   * Check if the current user has **at least one** of the given roles.
   *
   * @param roles - Roles to check.
   */
  userHasAnyRole(roles: string[]): boolean {
    const user = this.getUser();
    if (!user?.roles) return false;
    return roles.some((role) => user.roles!.includes(role));
  }

  /**
   * Check if the current user has **all** of the given roles.
   *
   * @param roles - Roles to check.
   */
  userHasAllRoles(roles: string[]): boolean {
    const user = this.getUser();
    if (!user?.roles) return false;
    return roles.every((role) => user.roles!.includes(role));
  }

  // -----------------------------------------------------------------------
  // Internal helpers
  // -----------------------------------------------------------------------

  /** @internal */
  private setToken(token: string): void {
    sessionStorage.setItem(TOKEN_STORAGE_KEY, token);
  }

  /** @internal */
  private setUser(user: CasUser): void {
    sessionStorage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
  }

  /** @internal Clear all CAS-related sessionStorage entries. */
  clearSession(): void {
    sessionStorage.removeItem(TOKEN_STORAGE_KEY);
    sessionStorage.removeItem(USER_STORAGE_KEY);
  }

  /**
   * @internal Remove the `token` query parameter from the URL without
   * triggering a page reload.
   */
  private cleanUrl(): void {
    try {
      const url = new URL(window.location.href);
      url.searchParams.delete('token');
      window.history.replaceState({}, document.title, url.toString());
    } catch {
      // Ignore — non-critical.
    }
  }
}
