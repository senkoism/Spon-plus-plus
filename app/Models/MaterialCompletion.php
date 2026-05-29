<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class MaterialCompletion extends Model
{
    protected $fillable = ['material_id', 'user_id', 'is_done', 'done_at'];
 
    protected $casts = [
        'is_done' => 'boolean',
        'done_at' => 'datetime',
    ];
 
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
