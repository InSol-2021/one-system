@extends('layouts.app')

@section('title', '404 - Page Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <h1 class="text-9xl font-bold text-gray-300">404</h1>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Page Not Found
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                The page you're looking for doesn't exist or has been moved.
            </p>
        </div>

        <div class="mt-8 space-y-6">
            <div class="rounded-md bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Available Options
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Return to the <a href="{{ route('login') }}" class="underline">login page</a></li>
                                <li>Visit the <a href="{{ route('user.dashboard') }}" class="underline">user dashboard</a></li>
                                <li>Check the URL for typos</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center space-x-4">
                <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Go to Login
                </a>
                <a href="{{ route('user.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    User Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection