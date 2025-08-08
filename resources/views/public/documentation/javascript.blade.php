@extends('public.documentation.layout')

@section('title', 'JavaScript/HTML CAS SSO Integration Guide')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <div class="bg-yellow-100 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                <i class="fab fa-js text-yellow-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $javascriptGuide['title'] }}</h1>
                <p class="text-gray-600 mt-1">{{ $javascriptGuide['description'] }}</p>
            </div>
        </div>
    </div>

    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">1. Frontend CAS Client</h2>
        <div class="code-block mb-6">
            <pre class="language-javascript"><code>class CasAuthClient {
    constructor(options) {
        this.serverUrl = options.serverUrl;
        this.callbackUrl = options.callbackUrl;
    }

    login(returnUrl = '/') {
        const loginUrl = `${this.serverUrl}/auth/login`;
        const callbackUrl = `${this.callbackUrl}?return_url=${encodeURIComponent(returnUrl)}`;
        window.location.href = `${loginUrl}?callback_url=${encodeURIComponent(callbackUrl)}`;
    }

    async validateToken(token) {
        try {
            const response = await fetch('/api/validate-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            if (response.ok) {
                return await response.json();
            }
            throw new Error('Token validation failed');
        } catch (error) {
            throw error;
        }
    }
}</code></pre>
        </div>
    </section>

    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">2. HTML Example</h2>
        <div class="code-block mb-6">
            <pre class="language-html"><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;CAS Demo&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;div id="user-info"&gt;&lt;/div&gt;
    &lt;button onclick="login()"&gt;Login&lt;/button&gt;
    &lt;button onclick="logout()"&gt;Logout&lt;/button&gt;

    &lt;script&gt;
        const casAuth = new CasAuthClient({
            serverUrl: 'http://localhost:5000',
            callbackUrl: 'http://localhost:3000/cas/callback'
        });

        function login() {
            casAuth.login(window.location.pathname);
        }

        function logout() {
            localStorage.removeItem('casToken');
            location.reload();
        }
    &lt;/script&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
        </div>
    </section>

    <div class="bg-blue-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Next Steps</h2>
        <ul class="space-y-2">
            <li>• <a href="{{ route('docs.api.overview') }}" class="text-blue-600 hover:text-blue-800">API Reference</a></li>
            <li>• <a href="{{ route('docs.examples') }}" class="text-blue-600 hover:text-blue-800">More Examples</a></li>
        </ul>
    </div>
</div>
@endsection