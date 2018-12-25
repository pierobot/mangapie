<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnHold extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }
}
