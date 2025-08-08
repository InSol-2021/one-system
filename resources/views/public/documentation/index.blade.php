@extends('public.documentation.layout')

@section('title', 'CAS SSO Integration Documentation')
@section('description', 'Complete integration guide for CAS Single Sign-On authentication system supporting Laravel, .NET, Node.js, Java, Python, and more.')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 text-white py-20 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 40px 40px;"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div class="max-w-4xl mx-auto">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-500/20 rounded-full mb-8">
                <i class="fas fa-shield-alt text-blue-300 text-3xl"></i>
            </div>
            <h1 class="text-5xl md:text-6xl font-bold mb-6 text-white">
                CAS SSO Integration Guide
            </h1>
            <p class="text-xl md:text-2xl mb-10 text-slate-200 leading-relaxed">
                Complete documentation for integrating your applications with our enterprise-grade Single Sign-On authentication system.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#platforms" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-rocket mr-2"></i>Get Started
                </a>
                <a href="{{ route('docs.api.overview') }}" class="bg-white/10 backdrop-blur-sm border border-white/30 text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition-all duration-300">
                    <i class="fas fa-code mr-2"></i>API Reference
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-slate-900 mb-6">Why Choose CAS SSO?</h2>
            <p class="text-xl text-slate-600 max-w-3xl mx-auto">Enterprise-grade authentication with seamless integration across multiple platforms</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="group text-center p-8 bg-white border border-slate-200 rounded-2xl hover:border-blue-300 hover:shadow-xl transition-all duration-300">
                <div class="bg-blue-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-700 transition-colors">
                    <i class="fas fa-lock text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Secure Authentication</h3>
                <p class="text-slate-600 leading-relaxed">JWT tokens with HMAC-SHA256 signature validation and configurable expiration policies</p>
            </div>
            
            <div class="group text-center p-8 bg-white border border-slate-200 rounded-2xl hover:border-green-300 hover:shadow-xl transition-all duration-300">
                <div class="bg-green-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-green-700 transition-colors">
                    <i class="fas fa-cogs text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Multi-Platform Support</h3>
                <p class="text-slate-600 leading-relaxed">Native integration packages for Laravel, .NET, Node.js, Java, Python, and more</p>
            </div>
            
            <div class="group text-center p-8 bg-white border border-slate-200 rounded-2xl hover:border-purple-300 hover:shadow-xl transition-all duration-300">
                <div class="bg-purple-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-purple-700 transition-colors">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Comprehensive Logging</h3>
                <p class="text-slate-600 leading-relaxed">Full audit trail with IP tracking, user agent logging, and real-time monitoring</p>
            </div>
        </div>
    </div>
</section>

<!-- System Architecture Section -->
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-slate-900 mb-6">System Architecture</h2>
            <p class="text-xl text-slate-600 max-w-3xl mx-auto">Understanding our modular, enterprise-grade CAS design</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <div class="group bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200">
                <div class="flex items-center mb-6">
                    <div class="bg-blue-600 w-14 h-14 rounded-xl flex items-center justify-center mr-4 group-hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sitemap text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Complete Architecture Overview</h3>
                </div>
                <p class="text-slate-600 mb-8 leading-relaxed">
                    Detailed breakdown of our Admin/User/Public separation, Livewire components, PostgreSQL schema design, and comprehensive security architecture.
                </p>
                <a href="{{ route('docs.architecture') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold text-lg group-hover:translate-x-1 transition-transform">
                    View Architecture Guide <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            
            <div class="group bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200">
                <div class="flex items-center mb-6">
                    <div class="bg-green-600 w-14 h-14 rounded-xl flex items-center justify-center mr-4 group-hover:bg-green-700 transition-colors">
                        <i class="fas fa-database text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Database Schema Design</h3>
                </div>
                <p class="text-slate-600 mb-8 leading-relaxed">
                    Enterprise-grade PostgreSQL multi-schema architecture with proper security isolation, access controls, and audit trails.
                </p>
                <a href="{{ route('docs.architecture') }}#database-schema" class="inline-flex items-center text-green-600 hover:text-green-800 font-semibold text-lg group-hover:translate-x-1 transition-transform">
                    View Schema Details <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Platform Integration Section -->
<section id="platforms" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-slate-900 mb-6">Platform Integration Guides</h2>
            <p class="text-xl text-slate-600 max-w-3xl mx-auto">Choose your technology stack and get started in minutes with our comprehensive integration packages</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Laravel -->
            <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 hover:border-red-300">
                <div class="flex items-center mb-4">
                    <div class="bg-red-600 w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-red-700 transition-colors">
                        <i class="fab fa-laravel text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Laravel</h3>
                        <p class="text-slate-500 font-medium">PHP Framework</p>
                    </div>
                </div>
                <p class="text-slate-600 mb-6 leading-relaxed">
                    Complete Laravel integration with Composer package, middleware, and Eloquent models.
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500 font-medium">
                        <i class="fas fa-clock mr-1"></i>5 min setup
                    </span>
                    <a href="{{ route('docs.laravel') }}" class="text-blue-600 hover:text-blue-800 font-semibold group-hover:translate-x-1 transition-transform">
                        View Guide <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <!-- .NET MVC -->
            <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 hover:border-blue-300">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-600 w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-blue-700 transition-colors">
                        <i class="fab fa-microsoft text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">.NET MVC</h3>
                        <p class="text-slate-500 font-medium">C# Framework</p>
                    </div>
                </div>
                <p class="text-slate-600 mb-6 leading-relaxed">
                    ASP.NET MVC integration with JWT authentication and role-based authorization.
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500 font-medium">
                        <i class="fas fa-clock mr-1"></i>10 min setup
                    </span>
                    <a href="{{ route('docs.dotnet') }}" class="text-blue-600 hover:text-blue-800 font-semibold group-hover:translate-x-1 transition-transform">
                        View Guide <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <!-- Node.js -->
            <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 hover:border-green-300">
                <div class="flex items-center mb-4">
                    <div class="bg-green-600 w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-green-700 transition-colors">
                        <i class="fab fa-node-js text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Node.js</h3>
                        <p class="text-slate-500 font-medium">Express.js</p>
                    </div>
                </div>
                <p class="text-slate-600 mb-6 leading-relaxed">
                    Express.js middleware for JWT validation and session management.
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500 font-medium">
                        <i class="fas fa-clock mr-1"></i>3 min setup
                    </span>
                    <a href="{{ route('docs.nodejs') }}" class="text-blue-600 hover:text-blue-800 font-semibold group-hover:translate-x-1 transition-transform">
                        View Guide <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <!-- Java Spring -->
            <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 hover:border-orange-300">
                <div class="flex items-center mb-4">
                    <div class="bg-orange-600 w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-orange-700 transition-colors">
                        <i class="fab fa-java text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Java Spring</h3>
                        <p class="text-slate-500 font-medium">Spring Boot</p>
                    </div>
                </div>
                <p class="text-slate-600 mb-6 leading-relaxed">
                    Spring Security configuration with JWT authentication and method-level security.
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500 font-medium">
                        <i class="fas fa-clock mr-1"></i>8 min setup
                    </span>
                    <a href="{{ route('docs.java') }}" class="text-blue-600 hover:text-blue-800 font-semibold group-hover:translate-x-1 transition-transform">
                        View Guide <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <!-- Python Django -->
            <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 hover:border-indigo-300">
                <div class="flex items-center mb-4">
                    <div class="bg-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-indigo-700 transition-colors">
                        <i class="fab fa-python text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Python Django</h3>
                        <p class="text-slate-500 font-medium">Django Framework</p>
                    </div>
                </div>
                <p class="text-slate-600 mb-6 leading-relaxed">
                    Django middleware for authentication with custom user models and decorators.
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500 font-medium">
                        <i class="fas fa-clock mr-1"></i>7 min setup
                    </span>
                    <a href="{{ route('docs.python') }}" class="text-blue-600 hover:text-blue-800 font-semibold group-hover:translate-x-1 transition-transform">
                        View Guide <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <!-- JavaScript/HTML -->
            <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 hover:border-yellow-300">
                <div class="flex items-center mb-4">
                    <div class="bg-yellow-600 w-12 h-12 rounded-xl flex items-center justify-center mr-4 group-hover:bg-yellow-700 transition-colors">
                        <i class="fab fa-js text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">JavaScript</h3>
                        <p class="text-slate-500 font-medium">Frontend Integration</p>
                    </div>
                </div>
                <p class="text-slate-600 mb-6 leading-relaxed">
                    Pure JavaScript integration for SPAs and static websites with token management.
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500 font-medium">
                        <i class="fas fa-clock mr-1"></i>5 min setup
                    </span>
                    <a href="{{ route('docs.javascript') }}" class="text-blue-600 hover:text-blue-800 font-semibold group-hover:translate-x-1 transition-transform">
                        View Guide <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Start Section -->
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-slate-900 mb-6">Quick Start</h2>
            <p class="text-xl text-slate-600 max-w-3xl mx-auto">Get your application integrated in 3 simple steps with our streamlined setup process</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="group text-center">
                <div class="bg-blue-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-700 transition-colors shadow-lg">
                    <span class="text-white text-3xl font-bold">1</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Register Your App</h3>
                <p class="text-slate-600 leading-relaxed">Register your application with the CAS server to get client credentials and access keys</p>
            </div>
            
            <div class="group text-center">
                <div class="bg-green-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-green-700 transition-colors shadow-lg">
                    <span class="text-white text-3xl font-bold">2</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Install Package</h3>
                <p class="text-slate-600 leading-relaxed">Install the appropriate package or copy the integration code for your platform</p>
            </div>
            
            <div class="group text-center">
                <div class="bg-purple-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-purple-700 transition-colors shadow-lg">
                    <span class="text-white text-3xl font-bold">3</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Configure & Test</h3>
                <p class="text-slate-600 leading-relaxed">Set up your environment variables and test the authentication flow</p>
            </div>
        </div>
    </div>
</section>

<!-- Code Example Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-slate-900 mb-6">See It In Action</h2>
            <p class="text-xl text-slate-600 max-w-3xl mx-auto">Here's a quick example of how simple the integration is with our Laravel package</p>
        </div>
        
        <div class="bg-slate-900 rounded-2xl shadow-2xl p-8 max-w-4xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="bg-red-600 w-10 h-10 rounded-lg flex items-center justify-center mr-4">
                        <i class="fab fa-laravel text-white text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Laravel Example</h3>
                </div>
                <p class="text-slate-300">Protect your routes with a simple middleware configuration</p>
            </div>
            
            <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                <pre class="text-green-400 text-sm leading-relaxed"><code>// Install the package
<span class="text-blue-400">composer require</span> <span class="text-yellow-300">cas-system/laravel-client</span>

// In your routes/web.php
<span class="text-purple-400">Route</span>::<span class="text-green-400">middleware</span>([<span class="text-yellow-300">'cas.auth'</span>])-><span class="text-green-400">group</span>(<span class="text-blue-400">function</span> () {
    <span class="text-purple-400">Route</span>::<span class="text-green-400">get</span>(<span class="text-yellow-300">'/dashboard'</span>, [<span class="text-orange-400">DashboardController</span>::<span class="text-blue-400">class</span>, <span class="text-yellow-300">'index'</span>]);
    <span class="text-purple-400">Route</span>::<span class="text-green-400">get</span>(<span class="text-yellow-300">'/profile'</span>, [<span class="text-orange-400">ProfileController</span>::<span class="text-blue-400">class</span>, <span class="text-yellow-300">'show'</span>]);
});

// In your controller
<span class="text-blue-400">public function</span> <span class="text-green-400">index</span>()
{
    <span class="text-red-300">$user</span> = <span class="text-green-400">session</span>(<span class="text-yellow-300">'cas_user'</span>);
    <span class="text-blue-400">return</span> <span class="text-green-400">view</span>(<span class="text-yellow-300">'dashboard'</span>, <span class="text-green-400">compact</span>(<span class="text-yellow-300">'user'</span>));
}</code></pre>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 text-white py-20 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.1) 1px, transparent 0); background-size: 30px 30px;"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold mb-6 text-white">Ready to Get Started?</h2>
        <p class="text-xl md:text-2xl mb-12 text-blue-100 max-w-3xl mx-auto leading-relaxed">
            Choose your platform and start integrating with CAS SSO today. Complete setup in minutes with our comprehensive guides.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('docs.laravel') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 border border-white/30 hover:border-white/50 transform hover:-translate-y-1">
                <i class="fab fa-laravel mr-2"></i>Laravel Guide
            </a>
            <a href="{{ route('docs.dotnet') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 border border-white/30 hover:border-white/50 transform hover:-translate-y-1">
                <i class="fab fa-microsoft mr-2"></i>.NET Guide
            </a>
            <a href="{{ route('docs.nodejs') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 border border-white/30 hover:border-white/50 transform hover:-translate-y-1">
                <i class="fab fa-node-js mr-2"></i>Node.js Guide
            </a>
        </div>
        
        <!-- Additional Quick Links -->
        <div class="mt-16 pt-8 border-t border-white/20">
            <p class="text-blue-100 mb-6">Need something else?</p>
            <div class="flex flex-wrap justify-center gap-6 text-sm">
                <a href="{{ route('docs.api.overview') }}" class="text-white hover:text-blue-200 transition-colors">
                    <i class="fas fa-code mr-1"></i>API Reference
                </a>
                <a href="{{ route('docs.examples') }}" class="text-white hover:text-blue-200 transition-colors">
                    <i class="fas fa-play mr-1"></i>Code Examples
                </a>
                <a href="{{ route('docs.section', ['section' => 'authentication']) }}" class="text-white hover:text-blue-200 transition-colors">
                    <i class="fas fa-shield-alt mr-1"></i>Security Guide
                </a>
            </div>
        </div>
    </div>
</section>
@endsection