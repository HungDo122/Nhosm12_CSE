<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = ['name', 'code', 'description', 'logo', 'status'];

    public function members()
    {
        return $this->hasMany(ClubMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'club_members')
                    ->withPivot('role', 'is_manager')
                    ->withTimestamps();
    }

    public function leaders()
    {
        return $this->belongsToMany(User::class, 'club_members')
                    ->wherePivot('role', 'leader')
                    ->withPivot('role', 'is_manager')
                    ->withTimestamps();
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
