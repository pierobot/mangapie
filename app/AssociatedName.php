<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssociatedName extends Model
{
    //
    protected $fillable = ['name'];

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function references()
    {
        return $this->hasMany(AssociatedNameReference::class);
    }

    public function scopeSearch($query, $keywords)
    {
        return empty($keywords) ? $query : $query->whereRaw('match(name) against(? in boolean mode)', [$keywords]);
    }
}
