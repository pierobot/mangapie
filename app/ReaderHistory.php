<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReaderHistory extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getMangaId()
    {
        return $this->manga_id;
    }

    public function getArchiveName()
    {
        return $this->archive_name;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getPageCount()
    {
        return $this->page_count;
    }

    public function getLastUpdated()
    {
        return $this->updated_at;
    }

    public function manga()
    {
        return $this->belongsTo(\App\Manga::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
