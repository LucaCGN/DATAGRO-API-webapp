<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        Log::info('showLoginForm method called');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('login method called');
        $credentials = $request->only('email', 'password'); // Use 'email' and 'password' fields

        Log::info('Credentials: ', $credentials);

        if (Auth::attempt($credentials)) {
            Log::info('Login successful');
            return redirect()->intended('/');
        }

        Log::info('Login failed');
        return redirect()->back()->withErrors(['login' => 'Invalid login credentials.']);
    }

}
