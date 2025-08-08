@extends('layouts.auth')

@section('title', '2FA Verification')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-purple-100">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Two-Factor Authentication
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter the 6-digit code from your authenticator app or use a backup code
            </p>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('auth.2fa.verify') }}">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                </div>
            @endif

            @if (session('message'))
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="text-sm text-blue-600">
                        {{ session('message') }}
                    </div>
                </div>
            @endif

            <div>
                <label for="code" class="sr-only">Verification Code</label>
                <input id="code" name="code" type="text" required maxlength="8"
                       class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm text-center tracking-widest"
                       placeholder="Enter 6-digit code" autocomplete="off">
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-purple-500 group-hover:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    Verify
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm text-purple-600 hover:text-purple-500">
                    Back to login
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('code').focus();

document.getElementById('code').addEventListener('input', function(e) {
    if (e.target.value.length === 6 && /^\d{6}$/.test(e.target.value)) {
        setTimeout(() => {
            e.target.closest('form').submit();
        }, 500);
    }
});
</script>
@endsection
