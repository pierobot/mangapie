<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AssociatedNameReference extends Pivot
{
    protected $table = 'associated_name_references';

    public function associatedName()
    {
        return $this->belongsTo(AssociatedName::class);
    }

    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }
}
