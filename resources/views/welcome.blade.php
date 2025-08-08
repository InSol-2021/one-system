@extends('layouts.app')

@section('title', 'Laravel CAS - Central Authentication Service')

@section('content')
<section class="hero-section">
    <div class="hero-content">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: rgba(59, 130, 246, 0.2); border-radius: 50%; margin-bottom: 2rem;">
            <i class="fas fa-shield-alt" style="color: #93c5fd; font-size: 2rem;"></i>
        </div>
        <h1 class="hero-title">
            Central Authentication Service
        </h1>
        <p class="hero-subtitle">
            Enterprise-grade Single Sign-On solution built with Laravel. Secure, scalable, and easy to integrate.
        </p>
        <div class="access-cards">
            <div class="access-card">
                <div class="card-icon blue">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3 class="card-title">CAS Admin Panel</h3>
                <p class="card-description">Manage authentication, client systems, and monitor security events with comprehensive admin tools</p>
                <a href="{{ route('login') }}" class="card-button btn-white">
                    Access CAS Admin
                </a>
            </div>

            <div class="access-card">
                <div class="card-icon green">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <h3 class="card-title">User Dashboard</h3>
                <p class="card-description">User dashboard for client system linking and single sign-on access across applications</p>
                <a href="{{ route('user.dashboard') }}" class="card-button btn-green">
                    Access Dashboard
                </a>
            </div>

            <div class="access-card">
                <div class="card-icon purple">
                    <i class="fas fa-store"></i>
                </div>
                <h3 class="card-title">Customer Portal</h3>
                <p class="card-description">Demo customer portal showcasing seamless CAS SSO integration and user experience</p>
                <a href="http://localhost:9000" target="_blank" class="card-button btn-purple">
                    Demo Portal
                </a>
            </div>
        </div>
    </div>
</section>

<section class="features-section">
    <div class="features-content">
        <div class="features-header">
            <h2 class="features-title">Enterprise Features</h2>
            <p class="features-subtitle">Comprehensive authentication and security features designed for enterprise environments</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon blue">
                    <i class="fas fa-lock"></i>
                </div>
                <h4 class="feature-title">Secure Authentication</h4>
                <p class="feature-description">Enterprise-grade security with scrypt password hashing, JWT tokens, and HMAC-SHA256 validation</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon green">
                    <i class="fas fa-bolt"></i>
                </div>
                <h4 class="feature-title">SSO Integration</h4>
                <p class="feature-description">Seamless single sign-on across multiple client applications with comprehensive platform support</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon purple">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4 class="feature-title">Audit & Monitoring</h4>
                <p class="feature-description">Comprehensive audit trails, real-time monitoring, and detailed security event tracking</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon orange">
                    <i class="fab fa-docker"></i>
                </div>
                <h4 class="feature-title">Docker Ready</h4>
                <p class="feature-description">Complete containerization with nginx, PostgreSQL, Redis, and Kubernetes deployment support</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="cta-content">
        <h2 class="cta-title">Get Started Today</h2>
        <p class="cta-subtitle">
            Comprehensive documentation and integration guides for all major platforms and frameworks
        </p>

        <div class="cta-buttons">
            <a href="/docs" class="cta-button btn-primary">
                <i class="fas fa-book" style="margin-right: 0.5rem;"></i>View Documentation
            </a>
            <a href="/docs/laravel" class="cta-button btn-secondary">
                <i class="fab fa-laravel" style="margin-right: 0.5rem;"></i>Laravel Guide
            </a>
            <a href="/docs/examples" class="cta-button btn-secondary">
                <i class="fas fa-code" style="margin-right: 0.5rem;"></i>Code Examples
            </a>
        </div>
    </div>
</section>
@endsection
