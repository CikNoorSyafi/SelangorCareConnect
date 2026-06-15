<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerApplication extends Model
{
    protected $table = 'volunteer_applications';

    protected $fillable = [
        'user_id',
        'campaign_id',
        'shift',
        'skill',
        'notes',
        'status'

    ];

    public function campaign()
    {
        return $this->belongsTo(
            Campaign::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }
}