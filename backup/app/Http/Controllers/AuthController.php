<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // LOGIN FUNCTION
    public function login(Request $request)
    {
        $user = User::where(
            'email',
            $request->email
        )->first();

        if (
            !$user ||
            !Hash::check(
                $request->password,
                $user->password
            )
        ) {
            return back()
                ->with(
                    'error',
                    'Invalid credentials'
                );
        }

        session([
            'user' => [

                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role

            ]
        ]);

        if ($user->role == 'organizer') {
            return redirect('/dashboard');
        }

        if ($user->role == 'donor') {
            return redirect('/donor/dashboard');
        }

        if ($user->role == 'volunteer') {
            return redirect('/volunteer/dashboard');
        }

        return back();
    }

    public function register(Request $request)
    {
        $request->validate([

            'name' => 'required',

            'email' => 'required|email|unique:users,email',

            'password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/'
            ],

            'role' => 'required'

        ]);

        User::create([

            'name' => $request->name,

            'email' => $request->email,

            'password' => Hash::make(
                $request->password
            ),

            'role' => $request->role

        ]);

        return redirect('/auth')
            ->with(
                'success',
                'Registration successful. You may now sign in.'
            )
            ->with(
                'showLogin',
                true
            );
    }

    // LOGOUT FUNCTION
    public function logout()
    {
        session()->forget('user');
        session()->flush();

        return redirect('/auth');
    }
}