<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{



    public function index()
    {
        $user = User::findOrFail(
            session('user.id')
        );

        return view(
            'profile.index',
            compact('user')
        );
    }
    public function edit()
    {
        $user = User::findOrFail(
            session('user.id')
        );

        return view(
            'profile.edit',
            compact('user')
        );
    }

    public function update(Request $request)
    {
        $request->validate([

            'name' => 'required|max:255',

            'email' =>
                'required|email|unique:users,email,' .
                session('user.id'),

            'phone' => 'nullable|max:50',

            'organization' => 'nullable|max:255',

        ]);

        $user = User::findOrFail(
            session('user.id')
        );

        $user->update([

            'name' => $request->name,

            'email' => $request->email,

            'phone' => $request->phone,

            'organization' =>
                $request->organization,

            'campaign_notifications' =>
                $request->has(
                    'campaign_notifications'
                ),

            'volunteer_notifications' =>
                $request->has(
                    'volunteer_notifications'
                ),

            'donation_notifications' =>
                $request->has(
                    'donation_notifications'
                ),

            'communication_notifications' =>
                $request->has(
                    'communication_notifications'
                ),
        ]);

        session([
            'user' => [

                'id' =>
                    $user->id,

                'name' =>
                    $user->name,

                'email' =>
                    $user->email,

                'role' =>
                    $user->role
            ]
        ]);

        return redirect('/profile')
            ->with(
                'success',
                'Profile updated successfully.'
            );
    }

    public function changePassword(Request $request)
    {
        $user = User::findOrFail(
            session('user.id')
        );
        $request->validate([

            'current_password' => 'required',

            'new_password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/'
            ],

            'confirm_password' => 'required'

        ]);

        if (
            !Hash::check(
                $request->current_password,
                $user->password
            )
        ) {
            return back()
                ->with(
                    'error',
                    'Current password is incorrect.'
                );
        }

        if (
            $request->new_password
            !=
            $request->confirm_password
        ) {
            return back()
                ->with(
                    'error',
                    'Password confirmation does not match.'
                );
        }

        $user->update([

            'password' =>
                Hash::make(
                    $request->new_password
                )

        ]);

        return redirect('/profile')
            ->with(
                'success',
                'Password updated successfully.'
            );
    }

    public function password()
    {
        return view(
            'profile.password'
        );
    }
}