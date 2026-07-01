<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset password — One System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

            // Monochrome-leaning scale: danger → warning → accent → success
            const colors = [
                'var(--color-danger)',
                'var(--color-warning)',
                'var(--color-warning)',
                'var(--color-accent)',
                'var(--color-success)',
            ];
            const textColors = [
                'var(--color-danger)',
                'var(--color-warning)',
                'var(--color-warning)',
                'var(--color-accent)',
                'var(--color-success)',
            ];
            const texts = ['Very weak', 'Weak', 'Fair', 'Good', 'Strong'];

            strengthIndicator.className = 'h-1.5 rounded-full transition-all duration-300';
            strengthIndicator.style.width = `${(strength / 5) * 100}%`;
            strengthIndicator.style.backgroundColor = strength > 0 ? colors[strength - 1] : 'var(--color-line-strong)';

            if (password.length > 0) {
                strengthText.textContent = strength > 0 ? texts[strength - 1] : 'Very weak';
                strengthText.className = 'mt-1.5 text-xs';
                strengthText.style.color = strength > 0 ? textColors[strength - 1] : 'var(--color-danger)';

                if (feedback.length > 0) {
                    strengthText.textContent += ` (missing: ${feedback.join(', ')})`;
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
                    matchIndicator.className = 'mt-1.5 text-xs';
                    matchIndicator.style.color = 'var(--color-success)';
                } else {
                    matchIndicator.textContent = '✗ Passwords do not match';
                    matchIndicator.className = 'mt-1.5 text-xs';
                    matchIndicator.style.color = 'var(--color-danger)';
                }
            } else {
                matchIndicator.textContent = '';
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center px-5 py-12">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <span class="os-icon-tile mx-auto">
                <i class="fa-solid fa-lock"></i>
            </span>
            <h2 class="mt-5 text-2xl font-semibold tracking-tight">Set a new password</h2>
            <p class="mt-1.5 text-sm text-[var(--color-muted)]">Choose a strong password for your account.</p>
        </div>

        <div class="os-card os-card-pad">
            <form class="space-y-5" action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ request('email') }}">

                @if($errors->any())
                    <div class="os-alert os-alert-danger">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label for="email" class="os-label">Email address</label>
                    <input id="email" name="email" type="email" required value="{{ request('email') }}" readonly
                           class="os-input bg-[var(--color-surface-2)] text-[var(--color-muted)] cursor-not-allowed">
                </div>

                <div>
                    <label for="password" class="os-label">New password</label>
                    <input id="password" name="password" type="password" required
                           oninput="checkPasswordStrength(); checkPasswordMatch();"
                           class="os-input"
                           placeholder="Enter your new password">
                    <div class="mt-2.5">
                        <div class="w-full bg-[var(--color-surface-2)] rounded-full h-1.5 overflow-hidden">
                            <div id="password-strength" class="h-1.5 rounded-full transition-all duration-300" style="width: 0%; background-color: var(--color-line-strong);"></div>
                        </div>
                        <p id="strength-text" class="mt-1.5 text-xs"></p>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="os-label">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           oninput="checkPasswordMatch();"
                           class="os-input"
                           placeholder="Confirm your new password">
                    <p id="password-match" class="mt-1.5 text-xs"></p>
                </div>

                <div class="rounded-[var(--radius-sm)] border border-[var(--color-line)] bg-[var(--color-surface-2)] p-4">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-info mt-0.5 text-[var(--color-muted)]"></i>
                        <div>
                            <h4 class="text-sm font-medium text-[var(--color-ink-2)]">Password requirements</h4>
                            <ul class="mt-1.5 list-disc space-y-1 pl-5 text-xs text-[var(--color-muted)]">
                                <li>At least 8 characters long</li>
                                <li>Contains uppercase and lowercase letters</li>
                                <li>Contains at least one number</li>
                                <li>Contains at least one special character</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <button type="submit" class="os-btn os-btn-primary os-btn-block os-btn-lg">Reset password</button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-[var(--color-muted)] hover:text-[var(--color-accent)]">
                        <i class="fa-solid fa-arrow-left text-xs"></i> Back to sign in
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
