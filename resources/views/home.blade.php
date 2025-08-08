@extends('layouts.app')

@section('title', 'CAS Authentication System - Home')

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
                <h3 class="card-title">Administrator Access</h3>
                <p class="card-description">Manage client systems, users, and security settings</p>
                <a href="{{ route('login') }}" class="card-button btn-white">
                    Admin Login
                </a>
            </div>

            <div class="access-card">
                <div class="card-icon green">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <h3 class="card-title">User Dashboard</h3>
                <p class="card-description">Access your linked client systems with single sign-on</p>
                <a href="{{ route('user.dashboard') }}" class="card-button btn-green">
                    User Access
                </a>
            </div>

            <div class="access-card">
                <div class="card-icon purple">
                    <i class="fas fa-book"></i>
                </div>
                <h3 class="card-title">API Documentation</h3>
                <p class="card-description">Integration guides and API documentation for developers</p>
                <a href="/docs" class="card-button btn-purple">
                    View Docs
                </a>
            </div>
        </div>

        <div style="margin-top: 2rem; padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2);">
            <div style="display: flex; align-items: center; justify-content: center; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                    <span style="color: #e5e7eb; font-size: 0.875rem;">System Online</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                    <span style="color: #e5e7eb; font-size: 0.875rem;">All Services Active</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="features-section">
    <div class="features-content">
        <div class="features-header">
            <h2 class="features-title">Enterprise Security Features</h2>
            <p class="features-subtitle">Comprehensive authentication and security features for enterprise environments</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon blue">
                    <i class="fas fa-shield-virus"></i>
                </div>
                <h4 class="feature-title">Multi-Factor Authentication</h4>
                <p class="feature-description">2FA with TOTP, QR code setup, and backup codes for enhanced security</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon green">
                    <i class="fas fa-robot"></i>
                </div>
                <h4 class="feature-title">reCAPTCHA Protection</h4>
                <p class="feature-description">Google reCAPTCHA v3 invisible protection against bots and automated attacks</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon purple">
                    <i class="fas fa-ban"></i>
                </div>
                <h4 class="feature-title">Account Lockout</h4>
                <p class="feature-description">Progressive security with automatic account lockout after failed attempts</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon orange">
                    <i class="fas fa-key"></i>
                </div>
                <h4 class="feature-title">Password Complexity</h4>
                <p class="feature-description">Enterprise-grade password requirements with strength validation</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon teal">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h4 class="feature-title">IP Whitelisting</h4>
                <p class="feature-description">Network-level access control with comprehensive IP management</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon red">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h4 class="feature-title">Comprehensive Auditing</h4>
                <p class="feature-description">Detailed audit logs for all authentication events and security actions</p>
            </div>
        </div>
    </div>
</section>

<section style="padding: 80px 0; background: #f8fafc;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem; text-align: center;">
        <h2 style="font-size: 2.5rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">
            Easy Integration
        </h2>
        <p style="font-size: 1.125rem; color: #64748b; margin-bottom: 3rem; max-width: 600px; margin-left: auto; margin-right: auto;">
            Get started with our CAS system in minutes using our comprehensive APIs and client packages
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); text-align: left;">
                <h4 style="font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem;">REST API</h4>
                <p style="color: #64748b; margin-bottom: 1.5rem;">Simple HTTP-based authentication API for any technology stack</p>
                <code style="background: #f1f5f9; padding: 0.5rem; border-radius: 4px; font-size: 0.875rem; display: block;">
                    POST /api/sso/token<br>
                    GET /api/sso/validate
                </code>
            </div>

            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); text-align: left;">
                <h4 style="font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem;">Laravel Package</h4>
                <p style="color: #64748b; margin-bottom: 1.5rem;">Native Laravel integration with Composer package support</p>
                <code style="background: #f1f5f9; padding: 0.5rem; border-radius: 4px; font-size: 0.875rem; display: block;">
                    composer require one-system/client<br>
                    php artisan cas:install
                </code>
            </div>
        </div>
    </div>
</section>
@endsection
