<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckinLog extends Model
{
    protected $fillable = ['registration_id', 'checkin_time'];

    protected $casts = [
        'checkin_time' => 'datetime',
    ];

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'registration_id');
    }
}
