<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['classroom_id', 'title', 'order'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }
}
