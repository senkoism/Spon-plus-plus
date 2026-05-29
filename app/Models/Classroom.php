<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['name', 'description', 'join_code', 'banner_path'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'class_user')
                    ->withPivot('role', 'notes', 'last_accessed_at')
                    ->withTimestamps();
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function teachers()
    {
        return $this->users()->wherePivot('role', 'teacher');
    }

    public function members()
    {
        return $this->users()->wherePivot('role', 'member');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}
