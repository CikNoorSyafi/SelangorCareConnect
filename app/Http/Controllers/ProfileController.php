<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{



    public function index()
    {
        $profile = session('profile');

        if (!$profile) {

            $profile = [

                'name' => 'Noor Amera Shafinaz',
                'email' => 'amera@gmail.com',
                'phone' => '0123456789',

                'organization' => 'SelangorCareConnect+',

                'role' => 'Community Program Manager',

                'campaign_notifications' => true,
                'volunteer_notifications' => true,
                'donation_notifications' => true,
                'communication_notifications' => true,

                'profile_picture' => null,
            ];

            session(['profile' => $profile]);
        }

        return view(
            'profile.index',
            compact('profile')
        );
    }
    public function edit()
    {
        $profile = session('profile');

        return view(
            'profile.edit',
            compact('profile')
        );
    }

    public function update(Request $request)
    {
        $profile = session('profile', []);

        $profile['name'] = $request->name;
        $profile['email'] = $request->email;
        $profile['phone'] = $request->phone;
        $profile['organization'] = $request->organization;
        $profile['role'] = $request->role;

        $profile['campaign_notifications']
            = $request->has('campaign_notifications');

        $profile['volunteer_notifications']
            = $request->has('volunteer_notifications');

        $profile['donation_notifications']
            = $request->has('donation_notifications');

        $profile['communication_notifications']
            = $request->has('communication_notifications');

        session(['profile' => $profile]);

        return redirect('/profile')
            ->with(
                'success',
                'Profile updated successfully.'
            );
    }

    public function changePassword(Request $request)
    {
        return redirect('/profile')
            ->with(
                'success',
                'Password updated successfully.'
            );
    }

    public function password()
    {
        return view('profile.password');
    }
}