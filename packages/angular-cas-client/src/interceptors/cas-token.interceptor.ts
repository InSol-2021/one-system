import { Inject, Injectable, Optional } from '@angular/core';
import {
  HttpEvent,
  HttpHandler,
  HttpInterceptor,
  HttpRequest,
} from '@angular/common/http';
import { Observable } from 'rxjs';
import { InjectionToken } from '@angular/core';

import { CasClientService } from '../services/cas-client.service';

/**
 * Optional injection token to supply URL patterns that the interceptor
 * should attach the `Authorization` header to.
 *
 * When **not** provided every outgoing request receives the header (if a
 * token exists). When provided only requests whose URL matches at least one
 * of the patterns are intercepted.
 *
 * Each pattern is treated as a **prefix match**.
 *
 * @example
 * ```typescript
 * providers: [
 *   { provide: CAS_INTERCEPT_URLS, useValue: ['/api/', 'https://backend.example.com'] },
 * ]
 * ```
 */
export const CAS_INTERCEPT_URLS = new InjectionToken<string[]>(
  'CAS_INTERCEPT_URLS',
);

/**
 * HTTP interceptor that automatically attaches the CAS JWT token as a
 * `Bearer` token on outgoing HTTP requests.
 *
 * ### How it works
 *
 * 1. Reads the stored JWT from `sessionStorage` via {@link CasClientService}.
 * 2. If a token exists (and the request URL matches the configured patterns)
 *    it clones the request with an `Authorization: Bearer <token>` header.
 * 3. Otherwise the request passes through untouched.
 *
 * ### Setup
 *
 * Register the interceptor in your app module (or `CasModule.forRoot`
 * already does this for you):
 *
 * ```typescript
 * { provide: HTTP_INTERCEPTORS, useClass: CasTokenInterceptor, multi: true }
 * ```
 */
@Injectable()
export class CasTokenInterceptor implements HttpInterceptor {
  constructor(
    private readonly casClient: CasClientService,
    @Optional()
    @Inject(CAS_INTERCEPT_URLS)
    private readonly interceptUrls: string[] | null,
  ) {}

  /**
   * Intercept an outgoing HTTP request and optionally attach the
   * `Authorization` header.
   *
   * @param req  - The outgoing `HttpRequest`.
   * @param next - The next handler in the interceptor chain.
   * @returns An `Observable` of the `HttpEvent` stream.
   */
  intercept(
    req: HttpRequest<unknown>,
    next: HttpHandler,
  ): Observable<HttpEvent<unknown>> {
    const token = this.casClient.getToken();

    // No token → pass through
    if (!token) {
      return next.handle(req);
    }

    // If URL patterns are configured, only intercept matching requests
    if (this.interceptUrls && this.interceptUrls.length > 0) {
      const matches = this.interceptUrls.some((pattern) =>
        req.url.startsWith(pattern),
      );
      if (!matches) {
        return next.handle(req);
      }
    }

    const authReq = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`,
      },
    });

    return next.handle(authReq);
  }
}
