<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerSkill extends Model
{
    protected $fillable = [

        'volunteer_id',
        'skill_id'

    ];

    public function skill()
    {
        return $this->belongsTo(
            Skill::class
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