<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Campaign extends Model
{
    protected $fillable = [

        'organizer_id',
        'name',
        'type',
        'location',
        'latitude',
        'longitude',
        'target',
        'start_date',
        'end_date',
        'description',
        'status'

    ];
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'campaign_id');
    }

    public function applications()
    {
        return $this->hasMany(VolunteerApplication::class, 'campaign_id');
    }

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