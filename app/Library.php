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

        foreach (\File::directories($this->getPath()) as $path) {
            $manga = Manga::updateOrCreate([
                'name' => pathinfo($path, PATHINFO_FILENAME),
                'path' => $path,
                'library_id' => Library::where('name','=',$this->getName())->first()->id
            ]);
        }
    }
}
