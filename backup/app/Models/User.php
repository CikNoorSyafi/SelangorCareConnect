<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VolunteerSkill;

class User extends Model
{
    protected $fillable = [

        'name',
        'email',
        'password',
        'role',

        'phone',
        'organization',

        'campaign_notifications',
        'volunteer_notifications',
        'donation_notifications',
        'communication_notifications'
    ];
    public function volunteerSkills()
    {
        return $this->hasMany(
            VolunteerSkill::class,
            'volunteer_id'
        );
    }

}