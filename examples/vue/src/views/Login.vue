<script setup>
// -----------------------------------------------------------------------------
// LOCAL LOGIN — the app's OWN username/password form.
//
// This is independent of CAS SSO: it posts the credentials to the Express
// backend's POST /login (the same route that also serves the CAS
// link-validation contract), which validates against the SQLite user store and
// establishes the app's local server session.
//
// On success we ALSO mirror the user into the same sessionStorage keys the CAS
// SDK uses (`cas_user` + `cas_token`). That way the existing reactive auth UI
// (nav bar, <CasProtectedView>, route guard) shows "signed in" identically for
// a local login or a CAS login — a user can sign in EITHER way.
// -----------------------------------------------------------------------------
import { ref } from 'vue';

const username = ref('');
const password = ref('');
const error = ref('');
const submitting = ref(false);

async function onSubmit() {
  error.value = '';
  submitting.value = true;
  try {
    const res = await fetch('/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      // No `client_validation` field => this is a real browser login, so the
      // backend WILL create the app's local session.
      body: JSON.stringify({
        username: username.value,
        password: password.value,
      }),
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok || !data.success) {
      error.value = data.error || 'Invalid username or password.';
      return;
    }

    // Mirror into the SDK's session storage so the shared reactive auth state
    // reports "signed in". A sentinel token marks this as a local session.
    sessionStorage.setItem('cas_user', JSON.stringify(data.user));
    sessionStorage.setItem('cas_token', 'local-session');

    // Full navigation so the CasPlugin re-initialises its reactive state from
    // sessionStorage on the next page load (same effect as the CAS callback).
    window.location.href = '/dashboard';
  } catch (e) {
    error.value = 'Could not reach the server. Please try again.';
  } finally {
    submitting.value = false;
  }
}
</script>

<template>
  <div class="card login-card">
    <h2>Sign in</h2>
    <p class="muted">
      Sign in with a local account, or use
      <RouterLink to="/">single sign-on</RouterLink> instead.
    </p>

    <form class="login-form" @submit.prevent="onSubmit">
      <label class="field">
        <span>Username</span>
        <input
          v-model="username"
          type="text"
          name="username"
          autocomplete="username"
          autofocus
          required
        />
      </label>

      <label class="field">
        <span>Password</span>
        <input
          v-model="password"
          type="password"
          name="password"
          autocomplete="current-password"
          required
        />
      </label>

      <p v-if="error" class="login-error">{{ error }}</p>

      <button class="btn primary block" type="submit" :disabled="submitting">
        {{ submitting ? 'Signing in…' : 'Sign in' }}
      </button>
    </form>

    <p class="muted hint">
      Demo accounts: <code>rajan / rajan123</code> &nbsp;·&nbsp;
      <code>demo / demo123</code>
    </p>
  </div>
</template>

<style scoped>
.login-card {
  max-width: 380px;
  margin: 0 auto;
}
.login-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-top: 18px;
}
.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-weight: 600;
  color: #52606d;
  font-size: 14px;
}
.field input {
  padding: 10px 12px;
  border: 1px solid #cbd2d9;
  border-radius: 6px;
  font-size: 15px;
  font-weight: 400;
  color: #1f2933;
}
.field input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}
.btn.block {
  width: 100%;
}
.login-error {
  margin: 0;
  color: #b91c1c;
  font-size: 14px;
}
.hint {
  margin-top: 18px;
  font-size: 13px;
}
.hint code {
  background: #f0f4f8;
  padding: 2px 6px;
  border-radius: 4px;
}
</style>
