package com.onesystem.examples.cas;

import com.cassystem.client.CasAuthFilter;
import com.cassystem.client.CasClient;
import com.cassystem.client.CasConfig;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.boot.web.servlet.FilterRegistrationBean;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

/**
 * Wires up the One System CAS client package.
 *
 * <p>All values come from the environment / application.properties (see
 * {@code .env.example}). The {@code clientSecret} is server-side only and is
 * required to validate tokens against {@code /api/validate-token}.</p>
 */
@Configuration
public class CasConfiguration {

    /**
     * Build the {@link CasClient} once and share it across the app.
     *
     * <p>{@link CasConfig} takes (serverUrl, clientId, clientSecret) and exposes a
     * builder-style {@code .callbackUrl(...)} setter. The callback URL must match
     * the URL registered for this client on the CAS server.</p>
     */
    @Bean
    public CasClient casClient(
            @Value("${cas.server-url}") String serverUrl,
            @Value("${cas.public-url:}") String publicUrl,
            @Value("${cas.client-id}") String clientId,
            @Value("${cas.client-secret}") String clientSecret,
            @Value("${cas.callback-url}") String callbackUrl) {

        // serverUrl is the INTERNAL back-channel base (token validation).
        // publicUrl is the PUBLIC, browser-facing base used only for the
        // /sso/login redirect; it falls back to serverUrl inside the client
        // when empty, so single-url local dev is unchanged.
        CasConfig config = new CasConfig(serverUrl, clientId, clientSecret)
                .publicUrl(publicUrl)
                .callbackUrl(callbackUrl);

        return new CasClient(config);
    }

    /**
     * Register the package's {@link CasAuthFilter} to guard /profile/*.
     *
     * <p>The filter looks for a "cas_user" attribute on the HTTP session (which our
     * {@code /callback} handler sets after a successful validation). When there is
     * no authenticated user it 302-redirects the browser to the configured login
     * path - here {@code /login}, which kicks off the CAS SSO flow.</p>
     *
     * <p>This demonstrates the package's intended "drop-in filter" pattern. The
     * public pages ({@code /}, {@code /login}, {@code /callback}, {@code /logout})
     * are deliberately left unguarded.</p>
     */
    @Bean
    public FilterRegistrationBean<CasAuthFilter> casAuthFilter(CasClient casClient) {
        FilterRegistrationBean<CasAuthFilter> reg = new FilterRegistrationBean<>();
        // CasAuthFilter(casClient, loginUrl): redirect unauthenticated users to /login.
        reg.setFilter(new CasAuthFilter(casClient, "/login"));
        reg.addUrlPatterns("/profile/*");
        reg.setOrder(1);
        return reg;
    }
}
