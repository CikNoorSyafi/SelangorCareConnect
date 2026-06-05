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
        'role'

    ];
    public function volunteerSkills()
    {
        return $this->hasMany(
            VolunteerSkill::class,
            'volunteer_id'
        );
    }

}