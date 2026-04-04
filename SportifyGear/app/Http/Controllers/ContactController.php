<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'email' => ['required', 'email:rfc,dns', 'max:100'],
            'message' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'name.required' => 'Please enter your name.',
            'name.min' => 'Name must be at least 3 characters.',
            'name.max' => 'Name cannot exceed 50 characters.',

            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.max' => 'Email cannot exceed 100 characters.',

            'message.required' => 'Message cannot be empty.',
            'message.min' => 'Message must be at least 10 characters.',
            'message.max' => 'Message cannot exceed 500 characters.',
        ]);


        Contact::create($validated);

        return back()->with('success', 'Message sent successfully!');
    }
}
