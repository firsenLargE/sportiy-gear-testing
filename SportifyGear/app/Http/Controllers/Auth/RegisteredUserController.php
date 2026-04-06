<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // Real-world validation rules
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\pL\s\-]+$/u', // Letters, spaces, hyphens only (unicode aware)
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns', // RFC + DNS check for real domain
                'max:255',
                'unique:' . User::class,
            ],
            'phone_no' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+\d\s\-\(\)]+$/', // Allows +, digits, spaces, hyphens, parentheses
                'unique:' . User::class, // Phone should be unique if provided
            ],
            'gender' => [
                'nullable',
                'string',
                'in:male,female,other',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)          // Minimum 8 characters
                    ->mixedCase()         // At least one uppercase & lowercase
                    ->letters()           // At least one letter
                    ->numbers()           // At least one number
                    ->symbols()           // At least one symbol
                    ->uncompromised(),    // Not found in data breaches
            ],
        ], [
            // Custom error messages
            'name.required' => 'Please enter your full name.',
            'name.regex' => 'Name may only contain letters, spaces, and hyphens.',
            'name.max' => 'Name cannot exceed 100 characters.',

            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address (e.g., name@domain.com).',
            'email.unique' => 'This email is already registered. Please login or use another email.',

            'phone_no.regex' => 'Phone number can only contain +, digits, spaces, hyphens, or parentheses.',
            'phone_no.unique' => 'This phone number is already associated with an account.',

            'gender.in' => 'Please select a valid gender option.',

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.uncompromised' => 'This password appears in a data breach. Please choose a different one.',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_no' => $validated['phone_no'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'password' => Hash::make($validated['password']),
            'account_status' => 'active',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect('/')->with('success', 'Account created successfully!');
    }
}
