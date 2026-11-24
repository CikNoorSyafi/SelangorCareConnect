<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

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

        if (!(bool) ($user->is_active ?? true)) {
            return back()->with('error', 'This account has been deactivated. Please contact the system administrator.');
        }

        if ($user->role === 'volunteer' && ($user->volunteer_status ?? 'Active') !== 'Active') {
            return back()->with('error', 'This volunteer account is inactive. Please contact the organizer.');
        }

        // Rotate the session identifier after successful authentication.
        $request->session()->regenerate();

        session([
            'user' => [

                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role

            ]
        ]);

        if ($user->role == 'administrator') {
            return redirect()->route('admin.dashboard');
        }

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

            'role' => 'required|in:volunteer,donor,organizer'

        ]);

        User::create([

            'name' => $request->name,

            'email' => $request->email,

            'password' => Hash::make(
                $request->password
            ),

            'role' => $request->role,
            'is_active' => true,
            'verification_status' => $request->role === 'organizer' ? 'Pending' : null,
            'volunteer_status' => $request->role === 'volunteer' ? 'Active' : null,

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


    // SHOW FORGOT PASSWORD FORM
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // SEND PASSWORD RESET LINK
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        // Keep the response generic so the page does not reveal whether an
        // email address is registered in the application.
        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors([
                'email' => 'Please wait before requesting another password reset link.',
            ])->withInput();
        }

        return back()->with(
            'status',
            'If an account exists for that email address, a password reset link has been sent.'
        );
    }

    // SHOW RESET PASSWORD FORM
    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    // SAVE NEW PASSWORD
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.regex' => 'The password must contain at least one uppercase letter and one number.',
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect('/auth')
                ->with('success', 'Your password has been reset successfully. Please sign in with your new password.')
                ->with('showLogin', true);
        }

        return back()
            ->withErrors([
                'email' => $status === Password::INVALID_TOKEN
                    ? 'This password reset link is invalid or has expired. Please request a new one.'
                    : 'We could not reset the password. Please request a new reset link.',
            ])
            ->withInput($request->only('email'));
    }

    // LOGOUT FUNCTION
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/auth');
    }
}