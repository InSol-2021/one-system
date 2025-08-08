@extends('public.documentation.layout')

@section('title', 'CAS SSO Code Examples')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Code Examples</h1>
        <p class="text-gray-600">Ready-to-use code examples for various scenarios and platforms</p>
    </div>

    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">Complete Integration Examples</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg border">
                <h3 class="text-lg font-semibold mb-2">Laravel Package</h3>
                <p class="text-gray-600 mb-4">Complete Laravel integration with middleware and views</p>
                <a href="{{ route('docs.laravel') }}" class="text-blue-600 hover:text-blue-800">View Guide →</a>
            </div>
            
            <div class="bg-white p-6 rounded-lg border">
                <h3 class="text-lg font-semibold mb-2">.NET MVC</h3>
                <p class="text-gray-600 mb-4">ASP.NET MVC with JWT authentication</p>
                <a href="{{ route('docs.dotnet') }}" class="text-blue-600 hover:text-blue-800">View Guide →</a>
            </div>
        </div>
    </section>

    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">Quick Start Example</h2>
        <div class="code-block mb-6">
            <pre class="language-bash"><code># 1. Register your application
curl -X POST http://localhost:5000/api/client-systems \
  -H "Content-Type: application/json" \
  -d '{
    "name": "My App",
    "url": "http://localhost:3000",
    "callback_url": "http://localhost:3000/cas/callback"
  }'

# 2. Set environment variables
export CAS_SERVER_URL=http://localhost:5000
export CAS_CLIENT_USERNAME=your_username
export CAS_CLIENT_PASSWORD=your_password

# 3. Integrate with your app</code></pre>
        </div>
    </section>

    <div class="bg-blue-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Need Help?</h2>
        <ul class="space-y-2">
            <li>• <a href="{{ route('docs.api.overview') }}" class="text-blue-600 hover:text-blue-800">API Reference</a></li>
            <li>• <a href="/" class="text-blue-600 hover:text-blue-800">CAS Dashboard</a></li>
        </ul>
    </div>
</div>
@endsection