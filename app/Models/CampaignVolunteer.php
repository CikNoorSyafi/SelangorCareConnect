<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignVolunteer extends Model
{
    protected $fillable = [

        'campaign_id',

        'volunteer_id',

        'role_id',

        'shift_id'

    ];
}