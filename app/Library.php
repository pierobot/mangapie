<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \App\Manga;

class Library extends Model
{
    //
    protected $fillable = ['name', 'path'];

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function scan()
    {
        // scan and add new directories
        foreach (\File::directories($this->getPath()) as $path) {
            $manga = Manga::updateOrCreate([
                'name' => pathinfo($path, PATHINFO_FILENAME),
                'path' => $path,
                'library_id' => Library::where('name','=',$this->getName())->first()->id
            ]);
        }

        // iterate through all the manga in the library
        // and remove those that no longer exist in the filesystem
        $manga = Manga::where('library_id', '=', $this->getId())->get();
        foreach ($manga as $manga_) {

            if (\File::exists($manga_->getPath()) === false) {

                $manga_->forceDelete();
            }
        }
    }

    public function forceDelete()
    {
        // get all the manga that have library_id to ours
        $manga = Manga::where('library_id', '=', $this->getId())->get();
        // and delete them
        foreach ($manga as $manga_) {

            // Manga::forceDelete deletes all the references to other tables (artists, authors, manga_information, etc..)
            $manga_->forceDelete();
        }

        parent::forceDelete();
    }
}
