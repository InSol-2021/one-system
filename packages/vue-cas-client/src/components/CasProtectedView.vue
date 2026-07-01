<script lang="ts">
/**
 * `<CasProtectedView>` — A slot-based component that conditionally renders
 * content based on the user's authentication and role state.
 *
 * - **Default slot** — rendered when the user is authenticated (and has the
 *   required roles, if specified).
 * - **`fallback` slot** — rendered when the user is NOT authenticated or
 *   does not have the required roles.
 * - **`loading` slot** — rendered while an auth operation is in-flight.
 *
 * @example
 * ```vue
 * <CasProtectedView :roles="['admin']" redirect>
 *   <AdminDashboard />
 *
 *   <template #fallback>
 *     <p>You do not have permission to view this page.</p>
 *   </template>
 *
 *   <template #loading>
 *     <p>Checking authentication…</p>
 *   </template>
 * </CasProtectedView>
 * ```
 */
export default {
  name: 'CasProtectedView',
};
</script>

<script setup lang="ts">
import { computed, watch } from 'vue';
import { useCasAuth } from '../composables/useCasAuth';
import { useCasUser } from '../composables/useCasUser';

/**
 * Component props.
 */
const props = withDefaults(
  defineProps<{
    /**
     * Optional roles required to render the default slot.
     * The user must have **all** specified roles.
     */
    roles?: string[];

    /**
     * When `true`, unauthenticated users are automatically redirected to
     * the CAS login page instead of rendering the fallback slot.
     *
     * @default false
     */
    redirect?: boolean;
  }>(),
  {
    roles: () => [],
    redirect: false,
  },
);

const { isAuthenticated, isLoading, login } = useCasAuth();
const { hasAllRoles } = useCasUser();

/** Whether the user meets all role requirements. */
const hasRequiredRoles = computed<boolean>(() => {
  if (props.roles.length === 0) return true;
  return hasAllRoles(props.roles).value;
});

/** Whether the default slot should be rendered. */
const isAuthorized = computed<boolean>(
  () => isAuthenticated.value && hasRequiredRoles.value,
);

// Auto-redirect when configured.
watch(
  [isAuthenticated, isLoading],
  ([authed, loading]) => {
    if (!loading && !authed && props.redirect) {
      login();
    }
  },
  { immediate: true },
);
</script>

<template>
  <slot v-if="isLoading" name="loading">
    <!-- Default loading state (can be overridden) -->
  </slot>
  <slot v-else-if="isAuthorized" />
  <slot v-else name="fallback">
    <!-- Default fallback (can be overridden) -->
  </slot>
</template>
