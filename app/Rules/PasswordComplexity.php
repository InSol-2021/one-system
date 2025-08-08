<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordComplexity implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check minimum length (8 characters)
        if (strlen($value) < 8) {
            $fail('Password must be at least 8 characters long.');
            return;
        }

        // Check for at least one uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            $fail('Password must contain at least one uppercase letter.');
            return;
        }

        // Check for at least one lowercase letter
        if (!preg_match('/[a-z]/', $value)) {
            $fail('Password must contain at least one lowercase letter.');
            return;
        }

        // Check for at least one number
        if (!preg_match('/[0-9]/', $value)) {
            $fail('Password must contain at least one number.');
            return;
        }

        // Check for at least one special character
        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('Password must contain at least one special character (!@#$%^&* etc.).');
            return;
        }

        // Check for common weak passwords
        $weakPasswords = [
            'password', 'password123', 'admin', 'admin123', 'qwerty', 
            '12345678', 'letmein', 'welcome', 'monkey', '1234567890'
        ];
        
        if (in_array(strtolower($value), $weakPasswords)) {
            $fail('This password is too common. Please choose a more secure password.');
            return;
        }

        // Check for repeated characters (more than 3 consecutive)
        if (preg_match('/(.)\1{3,}/', $value)) {
            $fail('Password cannot contain more than 3 consecutive identical characters.');
            return;
        }
    }
}