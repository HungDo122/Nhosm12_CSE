<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = ['name', 'description', 'manager_id'];

    // Một CLB có nhiều sự kiện
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // CLB thuộc về 1 user quản lý
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
