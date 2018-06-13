<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    public $fillable = ['manga_id', 'name', 'size'];

    public function manga()
    {
        return $this->belongsTo(\App\Manga::class);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    private static function convertSizeToReadable($bytes)
    {
        $sizes = [ 'B', 'KB', 'MB', 'GB' ];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return number_format(round($bytes, 2), 2) . ' ' . $sizes[$i];
    }

    public function getSize($asReadable = true)
    {
        return $asReadable == true ? self::convertSizeToReadable($this->size) : $this->size;
    }
}
