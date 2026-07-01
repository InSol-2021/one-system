/**
 * CAS SSO Client for the Browser
 * Lightweight JavaScript SDK for browser-based SSO integration.
 *
 * IMPORTANT: Token validation should always be done on your backend server.
 * This SDK handles login redirects, token extraction from URLs, and role checking.
 *
 * @version 1.0.0
 */
(function (root, factory) {
  if (typeof module === 'object' && module.exports) {
    module.exports = factory();
  } else {
    root.CasClient = factory();
  }
})(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  /**
   * @param {Object} config
   * @param {string} config.serverUrl - CAS server URL
   * @param {string} config.clientId - Registered client ID
   * @param {string} config.callbackUrl - OAuth callback URL
   * @param {string} [config.backendValidateUrl] - Your backend's token validation endpoint
   */
  function CasClient(config) {
    if (!config) throw new Error('config is required');
    if (!config.serverUrl) throw new Error('serverUrl is required');
    if (!config.clientId) throw new Error('clientId is required');

    this.config = {
      serverUrl: config.serverUrl.replace(/\/$/, ''),
      clientId: config.clientId,
      callbackUrl: config.callbackUrl || window.location.origin + '/cas/callback',
      backendValidateUrl: config.backendValidateUrl || '/api/auth/validate',
    };

    this._user = null;
  }

  /**
   * Generate CAS SSO login URL and redirect the browser.
   * @param {string} [returnUrl] - App URL to return to after a successful callback
   */
  CasClient.prototype.login = function (returnUrl) {
    if (returnUrl) {
      try { sessionStorage.setItem('cas_return_url', returnUrl); } catch (e) {}
    }
    var url = this.getLoginUrl();
    window.location.href = url;
  };

  /**
   * Generate CAS SSO login URL without redirecting.
   * The CAS server redirects to the client's registered callback_url with the
   * token appended, so the login request only needs the client_id.
   * @returns {string}
   */
  CasClient.prototype.getLoginUrl = function () {
    var params = new URLSearchParams({
      client_id: this.config.clientId,
    });
    return this.config.serverUrl + '/sso/login?' + params.toString();
  };

  /**
   * Read (and clear) the return URL stashed by login(), if any.
   * @returns {string|null}
   */
  CasClient.prototype.consumeReturnUrl = function () {
    try {
      var url = sessionStorage.getItem('cas_return_url');
      if (url) {
        sessionStorage.removeItem('cas_return_url');
        return url;
      }
    } catch (e) {}
    return null;
  };

  /**
   * Extract token from the current URL query string.
   * Call this on your callback page.
   * @returns {string|null} The token, or null if not found
   */
  CasClient.prototype.extractTokenFromUrl = function () {
    var params = new URLSearchParams(window.location.search);
    return params.get('token');
  };

  /**
   * Send token to your backend for server-side validation.
   * NEVER validate tokens in the browser — always delegate to your backend.
   *
   * @param {string} token - JWT token from CAS callback
   * @returns {Promise<Object|null>} User data from your backend
   */
  CasClient.prototype.validateTokenViaBackend = function (token) {
    var self = this;
    return fetch(this.config.backendValidateUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ token: token }),
    })
      .then(function (response) {
        if (!response.ok) return null;
        return response.json();
      })
      .then(function (data) {
        if (data && data.user) {
          self._user = data.user;
          self._storeUser(data.user);
          return data.user;
        }
        return null;
      })
      .catch(function (err) {
        console.error('[CAS] Backend validation failed:', err);
        return null;
      });
  };

  /**
   * Handle the full callback flow: extract token → validate via backend → store user.
   * @returns {Promise<Object|null>} User data or null
   */
  CasClient.prototype.handleCallback = function () {
    var token = this.extractTokenFromUrl();
    if (!token) {
      console.error('[CAS] No token found in URL');
      return Promise.resolve(null);
    }
    return this.validateTokenViaBackend(token);
  };

  /**
   * Get the currently stored user.
   * @returns {Object|null}
   */
  CasClient.prototype.getUser = function () {
    if (this._user) return this._user;
    try {
      var stored = sessionStorage.getItem('cas_user');
      if (stored) {
        this._user = JSON.parse(stored);
        return this._user;
      }
    } catch (e) {}
    return null;
  };

  /**
   * Check if user is authenticated.
   * @returns {boolean}
   */
  CasClient.prototype.isAuthenticated = function () {
    return this.getUser() !== null;
  };

  /**
   * Logout: clear stored user and redirect to CAS logout.
   * @param {string} [redirectUrl] - Where to go after logout
   */
  CasClient.prototype.logout = function (redirectUrl) {
    this._user = null;
    try { sessionStorage.removeItem('cas_user'); } catch (e) {}
    var returnUrl = redirectUrl || window.location.origin;
    var self = this;
    fetch(this.config.serverUrl + '/api/logout', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
    })
      .catch(function () {})
      .finally(function () {
        window.location.href = returnUrl;
      });
  };

  /**
   * Check if user has a specific role.
   * @param {string} role
   * @returns {boolean}
   */
  CasClient.prototype.userHasRole = function (role) {
    var user = this.getUser();
    if (!user || !user.roles) return false;
    return user.roles.indexOf(role) !== -1;
  };

  /**
   * Check if user has any of the specified roles.
   * @param {string[]} roles
   * @returns {boolean}
   */
  CasClient.prototype.userHasAnyRole = function (roles) {
    var user = this.getUser();
    if (!user || !user.roles) return false;
    for (var i = 0; i < roles.length; i++) {
      if (user.roles.indexOf(roles[i]) !== -1) return true;
    }
    return false;
  };

  /**
   * Check if user has all specified roles.
   * @param {string[]} roles
   * @returns {boolean}
   */
  CasClient.prototype.userHasAllRoles = function (roles) {
    var user = this.getUser();
    if (!user || !user.roles) return false;
    for (var i = 0; i < roles.length; i++) {
      if (user.roles.indexOf(roles[i]) === -1) return false;
    }
    return true;
  };

  // --- Internal ---

  CasClient.prototype._storeUser = function (user) {
    try { sessionStorage.setItem('cas_user', JSON.stringify(user)); } catch (e) {}
  };

  return CasClient;
});
