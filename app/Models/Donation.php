<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Donation extends Model
{
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    protected $fillable = [

        'user_id',
        'campaign_id',

        'contributor',
        'transaction_id',
        'campaign_type',

        'amount',

        'payment_method',
        'receipt_no',
        'frequency',
        'receipt_email',
        'dedication',

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