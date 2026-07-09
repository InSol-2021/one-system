import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Port the whole sample is served on (dev server AND the Express backend).
const PORT = Number(process.env.PORT || 9107);

export default defineConfig({
  plugins: [react()],

  server: {
    port: PORT,
    // In dev, the SPA runs on Vite (this port) and the Express API runs on a
    // separate internal port; proxy the backend routes to it so the browser only
    // ever talks to one origin. See server.js + the dev script. We point the
    // proxy at the backend's own port via API_PORT (defaults to PORT+1 in dev).
    //
    //   /api    -> CAS config + token validation + /api/me (local session)
    //   /login  -> local login form (GET) + local/CAS-validation login (POST)
    //   /logout -> clear the local session
    proxy: {
      '/api': {
        target: `http://localhost:${Number(process.env.API_PORT || PORT + 1)}`,
        changeOrigin: true,
      },
      '/login': {
        target: `http://localhost:${Number(process.env.API_PORT || PORT + 1)}`,
        changeOrigin: true,
      },
      '/logout': {
        target: `http://localhost:${Number(process.env.API_PORT || PORT + 1)}`,
        changeOrigin: true,
      },
    },
  },

  build: {
    outDir: 'dist',
  },
});
