<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReaderSettings extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determines whether the direction is left-to-right.
     *
     * @return bool
     */
    public function isLeftToRight()
    {
        return ! empty($this->direction) ? $this->direction === 'ltr' : false;
    }

    /**
     * Determines whether the direction is right-to-left.
     */
    /**
     * @return bool
     */
    public function isRightToLeft()
    {
        return ! empty($this->direction) ? $this->direction === 'rtl' : false;
    }

    /**
     * Determines whether the direction is vertical.
     * @return bool
     */
    public function isVertical()
    {
        return ! empty($this->direction) ? $this->direction === 'vrt' : false;
    }
}
