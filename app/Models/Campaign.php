<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [

        'name',
        'type',
        'location',
        'target',
        'start_date',
        'end_date',
        'description',
        'status'

    ];
    public function volunteers()
    {
        return $this->hasMany(
            CampaignVolunteer::class,
            'campaign_id'
        );
    }

    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'campaign_skills'
        );
    }

    public function campaignSkills()
    {
        return $this->hasMany(
            CampaignSkill::class,
            'campaign_id'
        );
    }
}