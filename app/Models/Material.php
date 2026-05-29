<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Material extends Model
{
    protected $fillable = ['classroom_id', 'user_id', 'section_id', 'title', 'content', 'file_path', 'is_pinned', 'type', 'open_date', 'due_date'];

    protected $casts = [
        'open_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function folderFiles()
    {
        return $this->hasMany(FolderFile::class);
    }
 
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
 
    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
 
    public function completions()
    {
        return $this->hasMany(MaterialCompletion::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
 
    public function isCompletedBy($userId)
    {
        return $this->completions()->where('user_id', $userId)->where('is_done', true)->exists();
    }

    public function getFileSizeHumanAttribute()
    {
        if (!$this->file_path || !\Illuminate\Support\Facades\Storage::exists($this->file_path)) {
            return null;
        }
        
        $bytes = \Illuminate\Support\Facades\Storage::size($this->file_path);
        if ($bytes <= 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 1) . ' ' . $units[$i];
    }
}
