<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // LOGIN FUNCTION
    public function login(Request $request)
    {
        $email = trim($request->email);
        $password = trim($request->password);


        if ($email === 'admin@test.com' && $password === '12345678') {

            session([
                'user' => [
                    'name' => 'Admin User',
                    'email' => $email,
                    'role' => 'organizer'
                ]
            ]);

            return redirect('/dashboard');
        }

        if ($email === 'donor@test.com' && $password === '12345678') {

            session([
                'user' => [
                    'name' => 'Amirul Hakim',
                    'email' => $email,
                    'role' => 'donor'
                ]
            ]);

            return redirect('/donor/dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }

    // LOGOUT FUNCTION
    public function logout()
    {
        session()->forget('user');
        session()->flush();

        return redirect('/auth');
    }
}