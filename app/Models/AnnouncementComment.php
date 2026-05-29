<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementComment extends Model
{
    protected $fillable = ['announcement_id', 'user_id', 'parent_id', 'body'];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(AnnouncementComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(AnnouncementComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}
