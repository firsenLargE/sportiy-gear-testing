<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    public function create(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns',
                'max:255',
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Enter a valid email address (e.g., name@domain.com).',
            'email.lowercase' => 'Email must be in lowercase.',
            'password.required' => 'Please enter a new password.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.uncompromised' => 'This password appears in a data breach. Please choose a different one.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
