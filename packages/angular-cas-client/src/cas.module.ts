import { ModuleWithProviders, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';

import { CAS_CONFIG, CasConfig } from './models/cas-config.model';
import { CasClientService } from './services/cas-client.service';
import { CasAuthService } from './services/cas-auth.service';
import { CasAuthGuard } from './guards/cas-auth.guard';
import {
  CasTokenInterceptor,
  CAS_INTERCEPT_URLS,
} from './interceptors/cas-token.interceptor';
import { CasCallbackComponent } from './components/cas-callback.component';

/**
 * Angular module that bundles every CAS authentication building block.
 *
 * ### Usage
 *
 * Import `CasModule.forRoot(config)` in your root `AppModule`:
 *
 * ```typescript
 * @NgModule({
 *   imports: [
 *     CasModule.forRoot({
 *       serverUrl: 'https://cas.example.com',
 *       clientId: 'my-app',
 *       callbackUrl: 'https://my-app.com/cas/callback',
 *       backendValidateUrl: '/api/auth/validate',
 *     }),
 *   ],
 * })
 * export class AppModule {}
 * ```
 *
 * The `forRoot()` call provides:
 *
 * | Symbol                   | Kind          |
 * | ------------------------ | ------------- |
 * | `CAS_CONFIG`             | InjectionToken |
 * | `CasClientService`       | Service       |
 * | `CasAuthService`         | Service       |
 * | `CasAuthGuard`           | Guard         |
 * | `CasTokenInterceptor`    | Interceptor   |
 * | `CasCallbackComponent`   | Component     |
 */
@NgModule({
  imports: [CommonModule, HttpClientModule, CasCallbackComponent],
  exports: [CasCallbackComponent],
})
export class CasModule {
  /**
   * Configure the CAS module at the application root level.
   *
   * @param config - CAS server configuration.
   * @param interceptUrls - Optional array of URL prefixes. When provided only
   *   requests matching these prefixes receive the `Authorization` header.
   * @returns A `ModuleWithProviders` suitable for the root `imports` array.
   */
  static forRoot(
    config: CasConfig,
    interceptUrls?: string[],
  ): ModuleWithProviders<CasModule> {
    return {
      ngModule: CasModule,
      providers: [
        { provide: CAS_CONFIG, useValue: config },
        CasClientService,
        CasAuthService,
        CasAuthGuard,
        {
          provide: HTTP_INTERCEPTORS,
          useClass: CasTokenInterceptor,
          multi: true,
        },
        ...(interceptUrls
          ? [{ provide: CAS_INTERCEPT_URLS, useValue: interceptUrls }]
          : []),
      ],
    };
  }
}
