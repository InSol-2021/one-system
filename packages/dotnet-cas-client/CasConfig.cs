namespace CasSystem.Client;

/// <summary>
/// Configuration for CAS SSO Client.
/// </summary>
public class CasConfig
{
    /// <summary>CAS server URL (e.g., https://your-cas-server.com)</summary>
    /// <remarks>Internal/back-channel base used for server-to-server token validation.</remarks>
    public string ServerUrl { get; set; } = "";

    /// <summary>
    /// Public, browser-facing CAS base URL used ONLY to build the SSO login redirect.
    /// In a split-horizon deployment the browser must reach CAS at a public host that
    /// differs from the internal back-channel <see cref="ServerUrl"/>. Optional: when
    /// empty, the login URL falls back to <see cref="ServerUrl"/>.
    /// </summary>
    public string PublicUrl { get; set; } = "";

    /// <summary>Registered client ID</summary>
    public string ClientId { get; set; } = "";

    /// <summary>Registered client secret</summary>
    public string ClientSecret { get; set; } = "";

    /// <summary>OAuth callback URL</summary>
    public string CallbackUrl { get; set; } = "";

    /// <summary>HMAC signature secret</summary>
    public string SignatureSecret { get; set; } = "default-signature-secret";

    /// <summary>Enable HMAC request signing</summary>
    public bool EnableSignatureValidation { get; set; } = false;

    /// <summary>Request timeout in seconds</summary>
    public int TimeoutSeconds { get; set; } = 30;

    /// <summary>Verify SSL certificates</summary>
    public bool VerifySsl { get; set; } = true;
}
