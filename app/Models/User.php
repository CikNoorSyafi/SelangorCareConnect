<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'organization',
        'age',
        'volunteer_status',
        'notes',
        'is_active',
        'verification_status',
        'verification_note',
        'campaign_notifications',
        'volunteer_notifications',
        'donation_notifications',
        'communication_notifications'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'campaign_notifications' => 'boolean',
        'volunteer_notifications' => 'boolean',
        'donation_notifications' => 'boolean',
        'communication_notifications' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // =====================================
    // ORGANIZER RELATIONSHIP
    // =====================================

    public function organizedCampaigns()
    {
        return $this->hasMany(
            Campaign::class,
            'organizer_id'
        );
    }

    // =====================================
    // VOLUNTEER APPLICATION RELATIONSHIP
    // =====================================

    public function volunteerApplications()
    {
        return $this->hasMany(
            VolunteerApplication::class,
            'user_id'
        );
    }

    // =====================================
    // CAMPAIGN ASSIGNMENT RELATIONSHIP
    // =====================================

    public function campaignAssignments()
    {
        return $this->hasMany(
            CampaignVolunteer::class,
            'volunteer_id'
        );
    }

    // =====================================
    // VOLUNTEER SKILLS RELATIONSHIP
    // =====================================

    public function volunteerSkills()
    {
        return $this->hasMany(
            VolunteerSkill::class,
            'volunteer_id'
        );
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
}