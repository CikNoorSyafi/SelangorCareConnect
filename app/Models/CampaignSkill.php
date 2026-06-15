<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignSkill extends Model
{
    protected $fillable = [
        'campaign_id',
        'skill_id'
    ];

    public function skill()
    {
        return $this->belongsTo(
            Skill::class,
            'skill_id'
        );
    }
}