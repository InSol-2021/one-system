import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';

import { CasAuthService } from '../services/cas-auth.service';

/**
 * Standalone callback component that processes the CAS SSO redirect.
 *
 * ### What it does
 *
 * 1. Extracts the `?token=…` query parameter from the URL.
 * 2. Validates the token via the backend (or CAS server).
 * 3. On success, navigates to the stored return URL (or `'/'`).
 * 4. On failure, navigates to `'/login'`.
 *
 * ### Route setup
 *
 * ```typescript
 * { path: 'cas/callback', component: CasCallbackComponent }
 * ```
 *
 * The component is **standalone** so it can be used without importing
 * `CasModule`, but it is also exported by `CasModule` for convenience.
 */
@Component({
  selector: 'cas-callback',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="cas-callback" *ngIf="isLoading">
      <div class="cas-callback__spinner"></div>
      <p class="cas-callback__text">Authenticating…</p>
    </div>
    <div class="cas-callback" *ngIf="error">
      <p class="cas-callback__error">{{ error }}</p>
    </div>
  `,
  styles: [
    `
      .cas-callback {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        font-family: sans-serif;
        color: #333;
      }
      .cas-callback__spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e0e0e0;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: cas-spin 0.8s linear infinite;
      }
      @keyframes cas-spin {
        to {
          transform: rotate(360deg);
        }
      }
      .cas-callback__text {
        margin-top: 16px;
        font-size: 1rem;
      }
      .cas-callback__error {
        color: #dc2626;
        font-size: 1rem;
      }
    `,
  ],
})
export class CasCallbackComponent implements OnInit {
  /** Whether the callback flow is still in progress. */
  isLoading = true;

  /** Error message displayed when the callback fails. */
  error: string | null = null;

  constructor(
    private readonly casAuth: CasAuthService,
    private readonly router: Router,
  ) {}

  /** @internal */
  ngOnInit(): void {
    this.casAuth.handleCallback().subscribe({
      next: (user) => {
        this.isLoading = false;

        if (user) {
          // Navigate to the originally-requested URL, or fall back to root.
          const returnUrl =
            typeof sessionStorage !== 'undefined'
              ? sessionStorage.getItem('cas_return_url') ?? '/'
              : '/';
          sessionStorage.removeItem('cas_return_url');
          this.router.navigateByUrl(returnUrl);
        } else {
          this.error = 'Authentication failed. Please try again.';
          setTimeout(() => this.router.navigateByUrl('/login'), 3000);
        }
      },
      error: () => {
        this.isLoading = false;
        this.error = 'An unexpected error occurred during authentication.';
        setTimeout(() => this.router.navigateByUrl('/login'), 3000);
      },
    });
  }
}
