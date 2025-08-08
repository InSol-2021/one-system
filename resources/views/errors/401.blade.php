@extends('layouts.app')

@section('title', '401 - Unauthorized')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <h1 class="text-9xl font-bold text-yellow-300">401</h1>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Unauthorized Access
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                You need to authenticate to access this resource.
            </p>
        </div>

        <div class="mt-8 space-y-6">
            <div class="rounded-md bg-yellow-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Authentication Required
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Please log in with your credentials to access the CAS system.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center">
                <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Login to CAS
                </a>
            </div>
        </div>
    </div>
</div>
@endsection