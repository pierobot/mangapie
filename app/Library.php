<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    //
    protected $fillable = ['name', 'path'];

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPath() {
        return $this->path;
    }
}
