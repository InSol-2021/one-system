package com.cassystem.client;

/**
 * Configuration for CAS SSO Client.
 */
public class CasConfig {
    private final String serverUrl;
    private final String clientId;
    private final String clientSecret;
    private String publicUrl = "";
    private String callbackUrl = "";
    private String signatureSecret = "default-signature-secret";
    private boolean enableSignatureValidation = false;
    private int timeoutSeconds = 30;
    private boolean verifySsl = true;

    /**
     * Create a new CAS configuration.
     *
     * @param serverUrl    CAS server URL (e.g., https://your-cas-server.com)
     * @param clientId     Registered client ID
     * @param clientSecret Registered client secret
     */
    public CasConfig(String serverUrl, String clientId, String clientSecret) {
        if (serverUrl == null || serverUrl.isEmpty()) throw new IllegalArgumentException("serverUrl is required");
        if (clientId == null || clientId.isEmpty()) throw new IllegalArgumentException("clientId is required");
        if (clientSecret == null || clientSecret.isEmpty()) throw new IllegalArgumentException("clientSecret is required");

        this.serverUrl = serverUrl.replaceAll("/$", "");
        this.clientId = clientId;
        this.clientSecret = clientSecret;
    }

    // Builder-style setters
    /**
     * Public, browser-facing CAS base URL used ONLY to build the browser login
     * redirect ({@link CasClient#getLoginUrl}). In split-horizon deployments the
     * browser must reach CAS at a different (public) host than the back-channel
     * {@code serverUrl} used for server-to-server token validation. When unset
     * (null/empty) the login URL falls back to {@code serverUrl}.
     */
    public CasConfig publicUrl(String url) {
        this.publicUrl = (url == null) ? "" : url.replaceAll("/$", "");
        return this;
    }
    public CasConfig callbackUrl(String url) { this.callbackUrl = url; return this; }
    public CasConfig signatureSecret(String secret) { this.signatureSecret = secret; return this; }
    public CasConfig enableSignatureValidation(boolean enable) { this.enableSignatureValidation = enable; return this; }
    public CasConfig timeoutSeconds(int timeout) { this.timeoutSeconds = timeout; return this; }
    public CasConfig verifySsl(boolean verify) { this.verifySsl = verify; return this; }

    // Getters
    public String getServerUrl() { return serverUrl; }
    /** Public, browser-facing CAS base for the login redirect (empty if unset). */
    public String getPublicUrl() { return publicUrl; }
    public String getClientId() { return clientId; }
    public String getClientSecret() { return clientSecret; }
    public String getCallbackUrl() { return callbackUrl; }
    public String getSignatureSecret() { return signatureSecret; }
    public boolean isEnableSignatureValidation() { return enableSignatureValidation; }
    public int getTimeoutSeconds() { return timeoutSeconds; }
    public boolean isVerifySsl() { return verifySsl; }
}
