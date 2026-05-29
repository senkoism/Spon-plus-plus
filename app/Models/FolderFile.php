<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolderFile extends Model
{
    protected $fillable = ['material_id', 'file_name', 'file_path', 'file_size'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
