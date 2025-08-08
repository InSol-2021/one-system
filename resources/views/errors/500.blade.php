@extends('layouts.app')

@section('title', '500 - Server Error')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <h1 class="text-9xl font-bold text-red-300">500</h1>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Server Error
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Something went wrong on our end. Please try again later.
            </p>
        </div>

        <div class="mt-8 space-y-6">
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            What happened?
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>An internal server error occurred. Our team has been notified and is working to fix the issue.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center space-x-4">
                <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Go to Login
                </a>
                <button onclick="window.location.reload()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Try Again
                </button>
            </div>
        </div>
    </div>
</div>
@endsection