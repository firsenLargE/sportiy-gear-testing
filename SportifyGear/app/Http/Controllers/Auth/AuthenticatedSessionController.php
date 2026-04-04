<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // Authenticate user
        $request->authenticate();

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        // Update last login timestamp safely
        $user = Auth::user();

        if ($user) {
            $user->last_login_at = now(); // Make sure 'last_login_at' column exists
            $user->save();
        }

        // Redirect to intended page
        return redirect()->intended('/');
    }

    /**
     * Destroy an authenticated session (logout).
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
