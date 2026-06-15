<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [

        'title',
        'message',
        'type',
        'audience',
        'campaign_id',
        'status',
        'recipients'
    ];

    public function campaign()
    {
        return $this->belongsTo(
            Campaign::class
        );
    }
    public function recipients()
    {
        return $this->hasMany(
            UserNotification::class
        );
    }

}