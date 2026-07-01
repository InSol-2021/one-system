<script setup>
// CALLBACK PAGE — the CAS server redirects here as:
//   {callback_url}?token=<jwt>
//
// handleCallback() (from the SDK) does the whole single-use-token dance:
//   1. reads ?token= from the URL
//   2. POSTs { token } to backendValidateUrl (/api/auth/validate)
//   3. our Express backend forwards it to {CAS}/api/validate-token with the
//      client_secret and returns { user }
//   4. the SDK stores the user/token in sessionStorage (the app's own session)
//   5. it strips ?token= from the URL so it can't be reused
//
// The token is single-use, so we validate exactly once here, then route on.
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useCasAuth } from '@one-system/vue-cas-client';

const router = useRouter();
const { handleCallback, error } = useCasAuth();
const failed = ref(false);

onMounted(async () => {
  try {
    await handleCallback();
    router.replace('/dashboard');
  } catch (e) {
    // error (from the SDK) is already populated; flag local failure for UI.
    failed.value = true;
    console.error('CAS authentication failed:', e);
  }
});
</script>

<template>
  <div class="card">
    <template v-if="failed">
      <h2>Authentication failed</h2>
      <p class="muted">{{ error || 'The token could not be validated.' }}</p>
      <RouterLink to="/">Back to home</RouterLink>
    </template>
    <template v-else>
      <h2>Signing you in…</h2>
      <p class="muted">Validating your token with the CAS server.</p>
    </template>
  </div>
</template>
