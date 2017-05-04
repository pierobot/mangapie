<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssociatedName extends Model
{
    //
    protected $fillable = ['name'];

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
}
