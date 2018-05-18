<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
use App\Manga;

class WatchReference extends Model
{
    public $fillable = ['user_id', 'manga_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }
}
