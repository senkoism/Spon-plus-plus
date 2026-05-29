<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'classroom_id', 
        'user_id', 
        'title', 
        'description', 
        'type', 
        'file_path', 
        'original_filename',
        'file_type',
        'open_date', 
        'due_date', 
        'order'
    ];

    protected $casts = [
        'open_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function comments()
    {
        return $this->hasMany(AnnouncementComment::class)->whereNull('parent_id')->orderBy('created_at', 'asc');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function folderFiles()
    {
        return $this->hasMany(FolderFile::class);
    }
}
