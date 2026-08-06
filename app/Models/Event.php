<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    // Scope: chỉ lấy sự kiện đã được duyệt
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Accessor: kiểm tra sự kiện đã hết slot chưa (dùng withCount)
    public function getIsFullAttribute(): bool
    {
        $count = $this->registrations_count ?? $this->registrations()->count();
        return $count >= $this->capacity;
    }
}