<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MangaViews extends Model
{
    protected $fillable = [
        'user_id',
        'manga_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }
}
