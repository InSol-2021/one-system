/**
 * Dev-server proxy for `ng serve` (port 4200).
 *
 * Forwards backend calls to the Express server on APP_PORT (9110) so the SPA
 * can reach them without CORS during development:
 *
 *   • /api/**   → backend (GET /api/config, POST /api/auth/validate)
 *   • /login    → backend POST /login (local username/password validation)
 *
 * The /login entry needs a `bypass` function: GET /login is the Angular ROUTE
 * (the login form, served by ng serve itself), while POST /login must reach the
 * Express backend. The bypass returns the SPA's index for non-POST /login
 * requests so the router renders the form; everything else is proxied.
 *
 * This .cjs config supersedes proxy.conf.json (which only covered /api). See
 * angular.json → serve.options.proxyConfig.
 */
module.exports = {
  '/api': {
    target: 'http://localhost:9110',
    secure: false,
    changeOrigin: true,
  },
  '/login': {
    target: 'http://localhost:9110',
    secure: false,
    changeOrigin: true,
    // Only proxy the POST (credential validation). Let GET /login fall through
    // to ng serve so the Angular router renders the login component.
    bypass(req) {
      if (req.method !== 'POST') {
        return '/index.html';
      }
      return null; // proxy it
    },
  },
};
