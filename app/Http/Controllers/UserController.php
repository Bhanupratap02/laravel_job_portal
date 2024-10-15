<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //
    public function create()
    {
        return view('users.register');
    }
    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);
        $formFields['password'] = bcrypt($formFields['password']);
        $user = User::create($formFields);

        // login
        auth()->login($user);

        return redirect('/')->with('message', 'User created and logged in successfully');
    }
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message', 'You have been logged out!');
    }
    public function login()
    {
        return view("users.login");
    }
    public function authenticate(Request $request)
    {
        $credentials = request()->validate([
            "email" => ["required", "email"],
            "password" => ["required"]
        ]);
        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended("/")->with("message", "Logged in Successfully!");
        } else {
            return back()->withErrors(['email' => 'The provided credentials do not match our records.
                    Please try again or register as a new user.'])
                ->onlyInput("email");
        };
    }
}
