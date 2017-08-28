<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = ['name', 'description'];

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public static function populate($genres) {

        if (count($genres) > 0) {

            foreach ($genres as $genre) {

                $genre_db = Genre::updateOrCreate([
                    'name' => $genre['name'],
                    'description' => $genre['description']
                ]);
            }
        }
    }

    public function scopeOldest($query) {

        $old = Genre::orderBy('updated_at', 'asc')->first();

        return $old;
    }
}
