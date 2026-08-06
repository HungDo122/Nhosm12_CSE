<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'student_code',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function managedClubs()
    {
        return $this->belongsToMany(Club::class, 'club_members')
                    ->wherePivot('is_manager', true)
                    ->withPivot('role', 'is_manager')
                    ->withTimestamps();
    }

    public function clubMemberships()
    {
        return $this->hasMany(ClubMember::class);
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function studentPoints()
    {
        return $this->hasMany(StudentPoint::class);
    }

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_members')
                    ->withPivot('role', 'is_manager')
                    ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClubManager(): bool
    {
        return $this->role === 'club_manager';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}
