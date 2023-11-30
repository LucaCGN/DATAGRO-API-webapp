<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
   public function showLoginForm()
   {
       return view('auth.login');
   }

   public function login(Request $request)
   {
       $credentials = $request->only('login', 'password');

       if ($credentials['login'] === config('app.login') && $credentials['password'] === config('app.password')) {
           return redirect()->route('main');
       }

       return redirect()->back()->withErrors(['login' => 'Invalid login credentials.']);
   }
}
