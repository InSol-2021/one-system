<script setup>
// Protected page (meta.requiresAuth: true). The casAuthGuard redirects to CAS
// login if there is no session, so by the time this renders we have a user.
//
// useCasAuth() -> authenticated user + logout action.
// useCasUser() -> reactive roles / role-check helpers.
import { useCasAuth, useCasUser } from '@cas-system/vue-cas-client';

const { user, logout } = useCasAuth();
const { roles } = useCasUser();

// Clear BOTH the local server session and the SDK session on logout.
async function handleLogout() {
  try {
    await fetch('/api/auth/local-logout', { method: 'POST' });
  } catch {
    // Non-fatal.
  }
  await logout();
}
</script>

<template>
  <div class="card">
    <h2>Dashboard</h2>
    <p class="muted">This route is protected by <code>casAuthGuard</code>.</p>

    <h3>Authenticated user</h3>
    <ul>
      <li><strong>ID:</strong> {{ user?.id }}</li>
      <li><strong>Username:</strong> {{ user?.username }}</li>
      <li><strong>Email:</strong> {{ user?.email }}</li>
      <li><strong>Roles:</strong> {{ roles.length ? roles.join(', ') : '—' }}</li>
    </ul>

    <pre>{{ JSON.stringify(user, null, 2) }}</pre>

    <button class="btn" @click="handleLogout()">Logout</button>
  </div>
</template>
