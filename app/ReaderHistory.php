<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReaderHistory extends Model
{
    protected $fillable = ['user_id', 'manga_id', 'archive_name', 'page', 'page_count'];

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
}
