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
        'status',
        'check_in',
        'check_out',
        'hours_served',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'hours_served' => 'integer',
    ];

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}
