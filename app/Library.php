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

    public function scan() {
        $libraries = Library::all();

        // Populate the manga in each library path
        foreach ($libraries as $library) {
            foreach (\File::directories($library['path']) as $path) {
                $manga = Manga::updateOrCreate([
                    'name' => pathinfo($path, PATHINFO_FILENAME),
                    'path' => $path,
                    'library_id' => Library::where('name','=',$library->name)->first()->id
                ]);
            }
        }
    }
}
