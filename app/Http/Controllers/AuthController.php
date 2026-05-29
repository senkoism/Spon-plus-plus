<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'], // can be email or username
            'password' => ['required'],
        ]);

        $loginValue = strtolower($request->login);
        $loginField = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginField => $loginValue, 'password' => $request->password])) {
            $request->session()->regenerate();
            
            // Single session login: update session_id
            auth()->user()->update(['session_id' => session()->getId()]);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'username' => ['required', 'string', 'lowercase', 'max:30', 'unique:users,username', 'regex:/^[a-z0-9_]+$/'],
            'email' => ['required', 'string', 'email', 'lowercase', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.regex' => 'Full name can only contain letters and spaces.',
            'username.regex' => 'Username can only contain lowercase letters, numbers, and underscores. No spaces allowed.',
            'email.lowercase' => 'Email address must be in lowercase.',
        ]);

        User::create([
            'name' => $request->name,
            'username' => strtolower($request->username),
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => 'student', // default role
        ]);

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }

    public function logoutIdle(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('info', 'You have been logged out due to inactivity.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
