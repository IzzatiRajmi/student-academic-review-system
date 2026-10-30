<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 300;

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Please enter your username and password.',
            'password.required' => 'Please enter your username and password.',
        ]);

        // Lock login for 5 minutes after 5 failed attempts (per username + IP)
        $key = Str::lower($credentials['username']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            return back()->withInput($request->only('username'))
                ->withErrors(['username' => "Too many failed attempts. Try again in {$minutes} minute(s)."]);
        }

        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($key, self::LOCKOUT_SECONDS);

            return back()->withInput($request->only('username'))
                ->withErrors(['username' => 'Incorrect username or password.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->route($request->user()->homeRoute());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
