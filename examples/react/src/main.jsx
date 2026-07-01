import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
// The CAS provider from the local @one-system/react-cas-client package.
// (Resolved to the package's TS source via the Vite alias -- see vite.config.js.)
import { CasProvider } from '@one-system/react-cas-client';
import App from './App.jsx';
import './styles.css';

/**
 * Root bootstrap.
 *
 * We fetch the CAS config from our OWN backend (GET /api/config) at startup
 * instead of hard-coding it. That endpoint returns the NON-secret fields the
 * SDK needs (serverUrl, clientId, callbackUrl) plus backendValidateUrl, which
 * tells the SDK to POST tokens to /api/auth/validate for server-to-server
 * validation. The client_secret stays on the backend and never reaches here.
 */
function Bootstrap() {
  const [config, setConfig] = useState(null);
  const [configError, setConfigError] = useState(null);

  useEffect(() => {
    fetch('/api/config')
      .then((r) => {
        if (!r.ok) throw new Error(`/api/config returned ${r.status}`);
        return r.json();
      })
      .then(setConfig)
      .catch((err) => setConfigError(err.message));
  }, []);

  if (configError) {
    return (
      <main className="card">
        <h1>Configuration error</h1>
        <p className="error">Could not load /api/config: {configError}</p>
        <p>Is the backend (server.js) running? See the README.</p>
      </main>
    );
  }

  if (!config) {
    return (
      <main className="card">
        <p>Loading configuration…</p>
      </main>
    );
  }

  // CasProvider auto-handles the ?token=... callback on mount: it extracts the
  // token, POSTs it to backendValidateUrl, stores the returned CasUser in
  // sessionStorage, cleans the URL, and exposes everything via useCasAuth().
  return (
    <CasProvider
      config={config}
      onAuthSuccess={(user) => console.log('[CAS] authenticated:', user.username)}
      onAuthError={(err) => console.error('[CAS] auth failed:', err)}
    >
      <App />
    </CasProvider>
  );
}

createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <Bootstrap />
  </React.StrictMode>,
);
