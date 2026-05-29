<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'username', 'password', 'session_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'class_user')
                    ->withPivot('role', 'notes', 'last_accessed_at')
                    ->withTimestamps();
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function accessibleAnnouncements()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_visibility');
    }

    public function announcementComments()
    {
        return $this->hasMany(AnnouncementComment::class);
    }

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
}
