<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    public $fillable = ['manga_id', 'name'];

    public function manga()
    {
        return $this->belongsTo(\App\Manga::class);
    }

    public function getName()
    {
        return $this->name;
    }
}
