<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/[a-zA-Z]/',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . $request->user()->id,
            ],
            'phone_no' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
            ],
            'gender' => [
                'nullable',
                'in:male,female,other',
            ],
        ], [

            'name.required' => 'Full name is required.',
            'name.min'      => 'Full name must be at least 3 characters long.',
            'name.max'      => 'Full name cannot be longer than 100 characters.',
            'name.regex'    => 'Name cannot contain only numbers. Please include at least one letter.',

            'email.required' => 'Email address is required.',
            'email.email'    => 'Please enter a valid email address.',
            'email.unique'   => 'This email address is already taken by another user.',

            'phone_no.regex' => 'Please enter a valid phone number (only numbers, +, -, spaces and brackets allowed).',
            'phone_no.max'   => 'Phone number is too long (maximum 20 characters).',

            'gender.in'      => 'Please select a valid gender (Male, Female or Other).',
        ]);

        $user = $request->user();

        $user->fill([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone_no'  => $request->phone_no,
            'gender'    => $request->gender,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                PasswordRules::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'current_password.required'         => 'Current password is required.',
            'current_password.current_password' => 'The current password you entered is incorrect.',

            'password.required'     => 'New password is required.',
            'password.min'          => 'New password must be at least 8 characters long.',
            'password.letters'      => 'New password must contain at least one letter.',
            'password.mixedCase'    => 'New password must contain both uppercase and lowercase letters.',
            'password.numbers'      => 'New password must contain at least one number.',
            'password.symbols'      => 'New password must contain at least one symbol (like @, #, $, etc.).',
            'password.confirmed'    => 'Password confirmation does not match.',
            'password.uncompromised' => 'This password has appeared in a data leak. Please choose a different one.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}
