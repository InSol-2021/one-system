// -----------------------------------------------------------------------------
// Router. The `/dashboard` route is marked `meta.requiresAuth: true` so the
// casAuthGuard installed in main.js will redirect unauthenticated visitors to
// the CAS login page.
// -----------------------------------------------------------------------------
import { createRouter, createWebHistory } from 'vue-router';

import Home from '../views/Home.vue';
import Login from '../views/Login.vue';
import AuthCallback from '../views/AuthCallback.vue';
import Dashboard from '../views/Dashboard.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
  },
  {
    // Local username/password login (the app's own accounts). Independent of
    // the CAS SSO flow — a user may sign in EITHER way.
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    // The CAS-registered callback path. The CAS server redirects here with
    // `?token=<jwt>` after the user authenticates.
    path: '/auth/callback',
    name: 'callback',
    component: AuthCallback,
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    // Guarded: requires an authenticated CAS session.
    meta: { requiresAuth: true },
  },
];

export const router = createRouter({
  history: createWebHistory(),
  routes,
});
