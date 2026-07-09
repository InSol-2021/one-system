<script setup>
// Top-level shell: a tiny nav driven by reactive auth state from useCasAuth().
// The SAME reactive state covers BOTH auth paths, because a local login mirrors
// its user into the SDK's sessionStorage (see views/Login.vue).
import { RouterLink, RouterView } from 'vue-router';
import { useCasAuth } from '@cas-system/vue-cas-client';

const { isAuthenticated, user, login, logout } = useCasAuth();

// Logout must clear BOTH sessions: the app's local server session (cookie) AND
// the SDK's browser session. logout() from the SDK clears sessionStorage and
// redirects, so we tear down the server session first.
async function handleLogout() {
  try {
    await fetch('/api/auth/local-logout', { method: 'POST' });
  } catch {
    // Non-fatal: still clear the client session below.
  }
  await logout();
}
</script>

<template>
  <div class="page">
    <header class="bar">
      <nav class="nav">
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/dashboard">Dashboard</RouterLink>
      </nav>
      <div class="auth">
        <template v-if="isAuthenticated">
          <span class="who">{{ user?.username }}</span>
          <!-- Clears BOTH the local server session and the SDK session. -->
          <button class="btn" @click="handleLogout()">Logout</button>
        </template>
        <template v-else>
          <!-- Local username/password login (the app's own accounts). -->
          <RouterLink class="btn" to="/login">Sign in</RouterLink>
          <!-- login() redirects the browser to {CAS}/sso/login?client_id=... -->
          <button class="btn primary" @click="login()">Login with SSO</button>
        </template>
      </div>
    </header>

    <main class="content">
      <RouterView />
    </main>
  </div>
</template>

<style>
:root {
  font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
  color: #1f2933;
}
body {
  margin: 0;
  background: #f5f7fa;
}
.page {
  max-width: 760px;
  margin: 0 auto;
}
.bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 20px;
  background: #fff;
  border-bottom: 1px solid #e4e7eb;
}
.nav a {
  margin-right: 16px;
  text-decoration: none;
  color: #3e4c59;
  font-weight: 600;
}
.nav a.router-link-active {
  color: #2563eb;
}
.auth {
  display: flex;
  align-items: center;
  gap: 12px;
}
.who {
  font-weight: 600;
  color: #52606d;
}
.btn {
  border: 1px solid #cbd2d9;
  background: #fff;
  border-radius: 6px;
  padding: 8px 14px;
  font-weight: 600;
  cursor: pointer;
  display: inline-block;
  text-decoration: none;
  color: #1f2933;
  line-height: 1.2;
}
.btn.primary {
  background: #2563eb;
  border-color: #2563eb;
  color: #fff;
}
.content {
  padding: 28px 20px;
}
.card {
  background: #fff;
  border: 1px solid #e4e7eb;
  border-radius: 10px;
  padding: 22px;
}
.muted {
  color: #7b8794;
}
pre {
  background: #f0f4f8;
  padding: 14px;
  border-radius: 8px;
  overflow: auto;
}
</style>
