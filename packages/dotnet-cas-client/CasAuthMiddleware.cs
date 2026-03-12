using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.DependencyInjection;
using System.Text.Json;

namespace CasSystem.Client;

/// <summary>
/// ASP.NET Core middleware for CAS authentication.
/// </summary>
/// <example>
/// // In Program.cs or Startup.cs
/// builder.Services.AddSingleton(new CasConfig { ... });
/// builder.Services.AddSingleton&lt;CasClient&gt;();
/// app.UseMiddleware&lt;CasAuthMiddleware&gt;();
/// </example>
public class CasAuthMiddleware
{
    private readonly RequestDelegate _next;
    private readonly CasClient _casClient;
    private readonly string[] _protectedPaths;
    private readonly string _loginUrl;
    private readonly string[]? _requiredRoles;

    public CasAuthMiddleware(
        RequestDelegate next,
        CasClient casClient,
        string[]? protectedPaths = null,
        string loginUrl = "/auth/login",
        string[]? requiredRoles = null)
    {
        _next = next;
        _casClient = casClient;
        _protectedPaths = protectedPaths ?? new[] { "/dashboard", "/admin" };
        _loginUrl = loginUrl;
        _requiredRoles = requiredRoles;
    }

    public async Task InvokeAsync(HttpContext context)
    {
        var path = context.Request.Path.Value ?? "";
        var isProtected = _protectedPaths.Any(p => path.StartsWith(p, StringComparison.OrdinalIgnoreCase));

        if (!isProtected)
        {
            await _next(context);
            return;
        }

        // Check session
        var userJson = context.Session.GetString("cas_user");
        Dictionary<string, object>? casUser = null;

        if (!string.IsNullOrEmpty(userJson))
        {
            casUser = JsonSerializer.Deserialize<Dictionary<string, object>>(userJson);
        }

        // Check Authorization header
        if (casUser == null)
        {
            var authHeader = context.Request.Headers["Authorization"].FirstOrDefault();
            if (authHeader?.StartsWith("Bearer ") == true)
            {
                var token = authHeader[7..];
                casUser = _casClient.GetUserFromToken(token);
                casUser ??= await _casClient.ValidateTokenAsync(token);

                if (casUser != null)
                {
                    context.Session.SetString("cas_user", JsonSerializer.Serialize(casUser));
                }
            }
        }

        if (casUser == null)
        {
            if (context.Request.Headers["Accept"].Any(a => a?.Contains("application/json") == true))
            {
                context.Response.StatusCode = 401;
                context.Response.ContentType = "application/json";
                await context.Response.WriteAsync("{\"error\":\"Authentication required\"}");
                return;
            }
            context.Response.Redirect($"{_loginUrl}?return_url={path}");
            return;
        }

        // Check roles
        if (_requiredRoles?.Length > 0 && !CasClient.UserHasAnyRole(casUser, _requiredRoles))
        {
            context.Response.StatusCode = 403;
            context.Response.ContentType = "application/json";
            await context.Response.WriteAsync("{\"error\":\"Insufficient permissions\"}");
            return;
        }

        context.Items["cas_user"] = casUser;
        await _next(context);
    }
}

/// <summary>Extension methods for service registration.</summary>
public static class CasServiceExtensions
{
    /// <summary>Add CAS SSO client to dependency injection.</summary>
    public static IServiceCollection AddCasClient(this IServiceCollection services, CasConfig config)
    {
        services.AddSingleton(config);
        services.AddSingleton<CasClient>();
        return services;
    }
}
