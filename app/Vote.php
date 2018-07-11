<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function manga()
    {
        return $this->belongsTo(\App\Manga::class);
    }
}
