<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [

        'user_id',
        'campaign_id',
        'attendance_date',
        'check_in',
        'check_out',
        'hours_served'

    ];
}