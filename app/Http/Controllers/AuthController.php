<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'contact' => 'nullable|string|max:20',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact' => $request->contact,
        ]);

        return response()->json(['message' => 'Registration successful', 'user' => $user], 201);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log in the user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Get the authenticated user
            $user = Auth::user();

            // Check if the user is an admin and redirect to the admin dashboard
            if ($user->role == 'admin') {
                return response()->json(['message' => 'Login successful, redirecting to admin dashboard', 'user' => $user, 'redirect' => '/admin/dashboard']);
            }

            // For regular users, you can return them to the front store or another page
            return response()->json(['message' => 'Login successful, redirecting to the front store', 'user' => $user, 'redirect' => '/store']);
        }

        // Return an error response if login fails
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
