@extends('public.documentation.layout')

@section('title', 'Java Spring Boot CAS SSO Integration Guide')
@section('description', 'Complete guide for integrating Java Spring Boot applications with CAS Single Sign-On authentication system.')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <div class="bg-orange-100 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                <i class="fab fa-java text-orange-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $javaGuide['title'] }}</h1>
                <p class="text-gray-600 mt-1">{{ $javaGuide['description'] }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <span><i class="fas fa-clock mr-1"></i>Setup time: 8 minutes</span>
            <span><i class="fas fa-code mr-1"></i>Difficulty: Intermediate</span>
            <span><i class="fas fa-tag mr-1"></i>Spring Boot 2.7+</span>
        </div>
    </div>

    <!-- Dependencies -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">1. Dependencies (Maven)</h2>
        
        <div class="code-block mb-6">
            <pre class="language-xml"><code>&lt;dependencies&gt;
    &lt;dependency&gt;
        &lt;groupId&gt;org.springframework.boot&lt;/groupId&gt;
        &lt;artifactId&gt;spring-boot-starter-web&lt;/artifactId&gt;
    &lt;/dependency&gt;
    &lt;dependency&gt;
        &lt;groupId&gt;org.springframework.boot&lt;/groupId&gt;
        &lt;artifactId&gt;spring-boot-starter-security&lt;/artifactId&gt;
    &lt;/dependency&gt;
    &lt;dependency&gt;
        &lt;groupId&gt;io.jsonwebtoken&lt;/groupId&gt;
        &lt;artifactId&gt;jjwt-api&lt;/artifactId&gt;
        &lt;version&gt;0.11.5&lt;/version&gt;
    &lt;/dependency&gt;
    &lt;dependency&gt;
        &lt;groupId&gt;io.jsonwebtoken&lt;/groupId&gt;
        &lt;artifactId&gt;jjwt-impl&lt;/artifactId&gt;
        &lt;version&gt;0.11.5&lt;/version&gt;
        &lt;scope&gt;runtime&lt;/scope&gt;
    &lt;/dependency&gt;
    &lt;dependency&gt;
        &lt;groupId&gt;io.jsonwebtoken&lt;/groupId&gt;
        &lt;artifactId&gt;jjwt-jackson&lt;/artifactId&gt;
        &lt;version&gt;0.11.5&lt;/version&gt;
        &lt;scope&gt;runtime&lt;/scope&gt;
    &lt;/dependency&gt;
&lt;/dependencies&gt;</code></pre>
        </div>
    </section>

    <!-- Configuration -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">2. Configuration Properties</h2>
        
        <div class="code-block mb-6">
            <pre class="language-yaml"><code># application.yml
cas:
  server-url: http://localhost:5000
  client-id: your_client_id
  client-username: your_client_username
  client-password: your_client_password
  signature-secret: your_signature_secret
  callback-url: http://localhost:8080/cas/callback
  token-expiration: 120

spring:
  session:
    timeout: 7200s</code></pre>
        </div>
    </section>

    <!-- CAS Client Service -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">3. CAS Client Service</h2>
        
        <div class="code-block mb-6">
            <pre class="language-java"><code>// CasClientService.java
@@Service
public class CasClientService {
    
    @@Value("${cas.server-url}")
    private String serverUrl;
    
    @@Value("${cas.client-id}")
    private String clientId;
    
    @@Value("${cas.client-username}")
    private String clientUsername;
    
    @@Value("${cas.client-password}")
    private String clientPassword;
    
    @@Value("${cas.signature-secret}")
    private String signatureSecret;
    
    @@Value("${cas.callback-url}")
    private String callbackUrl;
    
    private final RestTemplate restTemplate;
    private final ObjectMapper objectMapper;
    
    public CasClientService() {
        this.restTemplate = new RestTemplate();
        this.objectMapper = new ObjectMapper();
    }
    
    public String getLoginUrl(String returnUrl) {
        String loginUrl = serverUrl + "/auth/login";
        String encodedCallbackUrl = URLEncoder.encode(
            callbackUrl + "?return_url=" + URLEncoder.encode(returnUrl, StandardCharsets.UTF_8),
            StandardCharsets.UTF_8
        );
        return loginUrl + "?callback_url=" + encodedCallbackUrl;
    }
    
    public CasUser validateToken(String token) {
        try {
            Claims claims = Jwts.parserBuilder()
                .setSigningKey(signatureSecret.getBytes())
                .build()
                .parseClaimsJws(token)
                .getBody();
            
            return CasUser.builder()
                .username(claims.getSubject())
                .email(claims.get("email", String.class))
                .role(claims.get("role", String.class))
                .firstName(claims.get("first_name", String.class))
                .lastName(claims.get("last_name", String.class))
                .build();
                
        } catch (Exception e) {
            throw new InvalidTokenException("Token validation failed", e);
        }
    }
    
    public AuthResult authenticate(String username, String password) {
        try {
            Map&lt;String, Object&gt; requestBody = Map.of(
                "username", username,
                "password", password,
                "client_id", clientId,
                "client_username", clientUsername,
                "client_password", clientPassword
            );
            
            HttpHeaders headers = new HttpHeaders();
            headers.setContentType(MediaType.APPLICATION_JSON);
            
            HttpEntity&lt;Map&lt;String, Object&gt;&gt; request = new HttpEntity&lt;&gt;(requestBody, headers);
            
            ResponseEntity&lt;String&gt; response = restTemplate.postForEntity(
                serverUrl + "/api/sso/token",
                request,
                String.class
            );
            
            return objectMapper.readValue(response.getBody(), AuthResult.class);
            
        } catch (Exception e) {
            throw new AuthenticationException("Authentication failed", e);
        }
    }
}</code></pre>
        </div>
    </section>

    <!-- Security Configuration -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">4. Security Configuration</h2>
        
        <div class="code-block mb-6">
            <pre class="language-java"><code>// SecurityConfig.java
@@Configuration
@@EnableWebSecurity
@@EnableMethodSecurity(prePostEnabled = true)
public class SecurityConfig {
    
    @@Bean
    public SecurityFilterChain filterChain(HttpSecurity http) throws Exception {
        http
            .authorizeHttpRequests(authz -> authz
                .requestMatchers("/", "/cas/**", "/login", "/error").permitAll()
                .requestMatchers("/admin/**").hasRole("ADMIN")
                .anyRequest().authenticated()
            )
            .formLogin(form -> form
                .loginPage("/login")
                .permitAll()
            )
            .logout(logout -> logout
                .logoutUrl("/logout")
                .logoutSuccessUrl("/")
                .permitAll()
            )
            .sessionManagement(session -> session
                .sessionCreationPolicy(SessionCreationPolicy.IF_REQUIRED)
                .maximumSessions(1)
                .maxSessionsPreventsLogin(false)
            )
            .csrf(csrf -> csrf.disable());
            
        return http.build();
    }
}</code></pre>
        </div>
    </section>

    <!-- Models -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">5. Model Classes</h2>
        
        <div class="code-block mb-6">
            <pre class="language-java"><code>// CasUser.java
@@Data
@@Builder
@@NoArgsConstructor
@@AllArgsConstructor
public class CasUser {
    private String username;
    private String email;
    private String role;
    private String firstName;
    private String lastName;
    
    public String getFullName() {
        return firstName + " " + lastName;
    }
    
    public boolean isAdmin() {
        return "admin".equals(role);
    }
    
    public boolean hasRole(String role) {
        return this.role.equals(role);
    }
}

// AuthResult.java
@@Data
@@NoArgsConstructor
@@AllArgsConstructor
public class AuthResult {
    private String token;
    private CasUser user;
    private LocalDateTime expiresAt;
}</code></pre>
        </div>
    </section>

    <!-- Controller -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">6. Controller Examples</h2>
        
        <div class="code-block mb-6">
            <pre class="language-java"><code>// HomeController.java
@@Controller
public class HomeController {
    
    private final CasClientService casClientService;
    
    public HomeController(CasClientService casClientService) {
        this.casClientService = casClientService;
    }
    
    @@GetMapping("/")
    public String index(Model model, HttpSession session) {
        CasUser user = (CasUser) session.getAttribute("casUser");
        model.addAttribute("user", user);
        return "index";
    }
    
    @@GetMapping("/dashboard")
    public String dashboard(Model model, HttpSession session) {
        CasUser user = (CasUser) session.getAttribute("casUser");
        if (user == null) {
            return "redirect:/login";
        }
        model.addAttribute("user", user);
        return "dashboard";
    }
    
    @@PreAuthorize("hasRole('ADMIN')")
    @@GetMapping("/admin")
    public String admin(Model model, HttpSession session) {
        CasUser user = (CasUser) session.getAttribute("casUser");
        model.addAttribute("user", user);
        return "admin";
    }
    
    @@GetMapping("/login")
    public String login(@@RequestParam(required = false) String returnUrl) {
        if (returnUrl == null) {
            returnUrl = "/dashboard";
        }
        String loginUrl = casClientService.getLoginUrl(returnUrl);
        return "redirect:" + loginUrl;
    }
    
    @@GetMapping("/cas/callback")
    public String casCallback(
        @@RequestParam String token,
        @@RequestParam(required = false) String return_url,
        HttpSession session
    ) {
        try {
            CasUser user = casClientService.validateToken(token);
            session.setAttribute("casUser", user);
            session.setAttribute("casToken", token);
            
            return "redirect:" + (return_url != null ? return_url : "/dashboard");
        } catch (Exception e) {
            return "redirect:/login?error=authentication_failed";
        }
    }
    
    @@GetMapping("/logout")
    public String logout(HttpSession session) {
        session.invalidate();
        return "redirect:/";
    }
}</code></pre>
        </div>
    </section>

    <!-- Custom Annotations -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">7. Custom Authentication</h2>
        
        <div class="code-block mb-6">
            <pre class="language-java"><code>// @@CasAuth annotation
@@Target({ElementType.METHOD, ElementType.TYPE})
@@Retention(RetentionPolicy.RUNTIME)
public @@interface CasAuth {
    String[] roles() default {};
}

// CasAuthAspect.java
@@Aspect
@@Component
public class CasAuthAspect {
    
    @@Around("@@annotation(casAuth)")
    public Object checkAuthentication(ProceedingJoinPoint joinPoint, CasAuth casAuth) throws Throwable {
        HttpServletRequest request = ((ServletRequestAttributes) RequestContextHolder.currentRequestAttributes()).getRequest();
        HttpSession session = request.getSession();
        
        CasUser user = (CasUser) session.getAttribute("casUser");
        
        if (user == null) {
            // Redirect to login
            HttpServletResponse response = ((ServletRequestAttributes) RequestContextHolder.currentRequestAttributes()).getResponse();
            response.sendRedirect("/login?returnUrl=" + request.getRequestURI());
            return null;
        }
        
        // Check roles if specified
        String[] requiredRoles = casAuth.roles();
        if (requiredRoles.length > 0) {
            boolean hasRole = Arrays.stream(requiredRoles)
                .anyMatch(role -> user.hasRole(role));
            
            if (!hasRole) {
                throw new AccessDeniedException("Insufficient permissions");
            }
        }
        
        return joinPoint.proceed();
    }
}</code></pre>
        </div>
    </section>

    <!-- Next Steps -->
    <div class="bg-blue-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Next Steps</h2>
        <ul class="space-y-2">
            <li>• <a href="/docs/api" class="text-blue-600 hover:text-blue-800">Explore the API Reference</a></li>
            <li>• <a href="/docs/examples" class="text-blue-600 hover:text-blue-800">View More Examples</a></li>
            <li>• <a href="/" class="text-blue-600 hover:text-blue-800">Test with CAS Dashboard</a></li>
        </ul>
    </div>
</div>
@endsection