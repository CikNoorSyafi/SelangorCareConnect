<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'organizer_id',
        'title',
        'message',
        'type',
        'audience',
        'campaign_id',
        'status',
        'recipients',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * This relationship is deliberately named userNotifications because
     * notifications.recipients is an integer column in the existing schema.
     */
    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }
}
