<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = ['club_id', 'category_id', 'name', 'description', 'location', 'capacity', 'start_time', 'end_time', 'status'];

    // Sự kiện thuộc về 1 CLB
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    // Một sự kiện có nhiều lượt đăng ký
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    // Sự kiện thuộc về 1 Category
    public function category()
    {
        return $this->belongsTo(EventCategory::class);
    }
}