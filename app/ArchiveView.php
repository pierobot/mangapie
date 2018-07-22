<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveView extends Model
{
    protected $fillable = [
        'user_id',
        'archive_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function archive()
    {
        return $this->belongsTo(Manga::class);
    }
}
