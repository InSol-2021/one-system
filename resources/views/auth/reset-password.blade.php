<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - CAS Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthIndicator = document.getElementById('password-strength');
            const strengthText = document.getElementById('strength-text');
            
            let strength = 0;
            let feedback = [];
            
            if (password.length >= 8) strength++;
            else feedback.push('At least 8 characters');
            
            if (/[a-z]/.test(password)) strength++;
            else feedback.push('Lowercase letter');
            
            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('Uppercase letter');
            
            if (/[0-9]/.test(password)) strength++;
            else feedback.push('Number');
            
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            else feedback.push('Special character');
            
            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
            const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            
            strengthIndicator.className = `h-2 rounded transition-all duration-300 ${colors[strength - 1] || 'bg-gray-300'}`;
            strengthIndicator.style.width = `${(strength / 5) * 100}%`;
            
            if (password.length > 0) {
                strengthText.textContent = strength > 0 ? texts[strength - 1] : 'Very Weak';
                strengthText.className = `text-xs mt-1 ${strength >= 4 ? 'text-green-600' : strength >= 3 ? 'text-blue-600' : strength >= 2 ? 'text-yellow-600' : 'text-red-600'}`;
                
                if (feedback.length > 0) {
                    strengthText.textContent += ` (Missing: ${feedback.join(', ')})`;
                }
            } else {
                strengthText.textContent = '';
            }
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const matchIndicator = document.getElementById('password-match');
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    matchIndicator.textContent = '✓ Passwords match';
                    matchIndicator.className = 'text-xs mt-1 text-green-600';
                } else {
                    matchIndicator.textContent = '✗ Passwords do not match';
                    matchIndicator.className = 'text-xs mt-1 text-red-600';
                }
            } else {
                matchIndicator.textContent = '';
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Set New Password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Choose a strong password for your account
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request('email') }}">
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input id="email" name="email" type="email" required value="{{ request('email') }}" readonly
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input id="password" name="password" type="password" required 
                       oninput="checkPasswordStrength(); checkPasswordMatch();"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter your new password">
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="password-strength" class="h-2 rounded transition-all duration-300 bg-gray-300" style="width: 0%"></div>
                    </div>
                    <p id="strength-text" class="text-xs mt-1"></p>
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required 
                       oninput="checkPasswordMatch();"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Confirm your new password">
                <p id="password-match" class="text-xs mt-1"></p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            @foreach($errors->all() as $error)
                                <p class="text-sm text-red-700">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Password Requirements</h4>
                        <div class="text-xs text-blue-700 mt-1">
                            <ul class="list-disc ml-5 space-y-1">
                                <li>At least 8 characters long</li>
                                <li>Contains uppercase and lowercase letters</li>
                                <li>Contains at least one number</li>
                                <li>Contains at least one special character</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Reset Password
                </button>
            </div>
            
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-500 transition-colors">
                    ← Back to Login
                </a>
            </div>
        </form>
    </div>
</body>
</html>