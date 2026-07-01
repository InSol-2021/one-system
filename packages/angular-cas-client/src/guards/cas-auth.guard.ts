import { Injectable } from '@angular/core';
import {
  CanActivate,
  ActivatedRouteSnapshot,
  RouterStateSnapshot,
} from '@angular/router';
import { Observable, map, take } from 'rxjs';

import { CasClientService } from '../services/cas-client.service';
import { CasAuthService } from '../services/cas-auth.service';

/**
 * Angular route guard that protects routes behind CAS authentication.
 *
 * If the user is not authenticated the guard redirects the browser to the
 * CAS login page (passing the current URL as the return destination).
 *
 * ### Basic usage
 *
 * ```typescript
 * const routes: Routes = [
 *   {
 *     path: 'dashboard',
 *     component: DashboardComponent,
 *     canActivate: [CasAuthGuard],
 *   },
 * ];
 * ```
 *
 * ### Role-based access
 *
 * Add a `roles` array to the route's `data` property to restrict access to
 * users that possess **at least one** of the listed roles:
 *
 * ```typescript
 * {
 *   path: 'admin',
 *   component: AdminComponent,
 *   canActivate: [CasAuthGuard],
 *   data: { roles: ['admin', 'superadmin'] },
 * }
 * ```
 */
@Injectable({ providedIn: 'root' })
export class CasAuthGuard implements CanActivate {
  constructor(
    private readonly casClient: CasClientService,
    private readonly casAuth: CasAuthService,
  ) {}

  /**
   * Determines whether the route can be activated.
   *
   * 1. If the user is **not authenticated** → redirect to CAS login.
   * 2. If `data.roles` is defined and the user lacks the required role(s) →
   *    deny access (`false`).
   * 3. Otherwise → allow (`true`).
   *
   * @param route  - The activated route snapshot.
   * @param state  - The router state snapshot (used for the return URL).
   * @returns An `Observable<boolean>` that resolves the access decision.
   */
  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot,
  ): Observable<boolean> {
    return this.casAuth.user$.pipe(
      take(1),
      map((user) => {
        // ── Not authenticated ─────────────────────────────────────────
        if (!user) {
          this.casClient.login(state.url);
          return false;
        }

        // ── Role check (if roles are specified on the route) ──────────
        const requiredRoles: string[] | undefined = route.data?.['roles'];
        if (requiredRoles && requiredRoles.length > 0) {
          const hasRole = this.casClient.userHasAnyRole(requiredRoles);
          if (!hasRole) {
            // Authenticated but lacking required role — deny silently.
            return false;
          }
        }

        return true;
      }),
    );
  }
}
