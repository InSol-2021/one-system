<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'login' => [
                'required_without:email',
                'string',
                'max:255'
            ],
            'email' => [
                'required_without:login',
                'string',
                'max:255'
            ],
            'password' => [
                'required',
                'string',
                'min:8'
            ],
            'g-recaptcha-response' => [
                'required',
                'recaptchav3:login,0.5'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'login.required_without' => 'Username or email address is required.',
            'email.required_without' => 'Email address is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'g-recaptcha-response.required' => 'Please complete the security verification.',
            'g-recaptcha-response.recaptchav3' => 'Security verification failed. Please try again.'
        ];
    }
}
