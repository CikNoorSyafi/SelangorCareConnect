<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Campaign;
use App\Models\VolunteerRole;
use App\Models\Shift;
use App\Models\User;

class CampaignVolunteer extends Model
{
    protected $fillable = [

        'campaign_id',

        'volunteer_id',

        'role_id',

        'shift_id'

    ];
    public function campaign()
    {
        return $this->belongsTo(
            Campaign::class
        );
    }

    public function role()
    {
        return $this->belongsTo(
            VolunteerRole::class
        );
    }

    public function shift()
    {
        return $this->belongsTo(
            Shift::class
        );
    }

    public function volunteer()
    {
        return $this->belongsTo(
            User::class,
            'volunteer_id'
        );
    }
}

