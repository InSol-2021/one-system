<script setup>
// Public landing page. Uses <CasProtectedView> to declaratively show either a
// "you're signed in" panel or a login prompt — no manual v-if branching.
//
// "Signed in" here means either path: a local username/password session OR a
// CAS SSO session (both populate the same reactive auth state).
import { computed } from 'vue';
import { CasProtectedView, useCasAuth } from '@one-system/vue-cas-client';

const { user, login } = useCasAuth();

// A local session is marked by the sentinel token written in Login.vue.
const isLocalSession = computed(
  () => sessionStorage.getItem('cas_token') === 'local-session',
);
</script>

<template>
  <div class="card">
    <h1>Vue CAS Client Sample</h1>
    <p class="muted">
      Minimal Vite + Vue 3 app demonstrating
      <code>@one-system/vue-cas-client</code> end-to-end: SSO login, server-side
      token validation, authenticated user, and logout.
    </p>

    <!--
      CasProtectedView renders its default slot only when authenticated,
      the #loading slot during async auth, and #fallback otherwise.
    -->
    <CasProtectedView>
      <p>
        You are signed in as <strong>{{ user?.username }}</strong>
        <span class="muted">
          (via {{ isLocalSession ? 'local account' : 'CAS single sign-on' }}).
        </span>
        Visit the <RouterLink to="/dashboard">Dashboard</RouterLink>.
      </p>

      <template #loading>
        <p class="muted">Checking authentication…</p>
      </template>

      <template #fallback>
        <p>You are not signed in. Choose how to sign in:</p>
        <div class="actions">
          <RouterLink class="btn" to="/login">
            Sign in with a local account
          </RouterLink>
          <button class="btn primary" @click="login()">Login with SSO</button>
        </div>
      </template>
    </CasProtectedView>
  </div>
</template>

<style scoped>
.actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 8px;
}
</style>
