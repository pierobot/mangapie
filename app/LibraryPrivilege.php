<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LibraryPrivilege extends Model
{
    //
    protected $fillable = ['user_id', 'library_id'];

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getLibraryId()
    {
        return $this->library_id;
    }

    public static function getIds()
    {
        if (\Auth::check() == false)
            return [];

        $user = \Auth::user();
        $libraries = null;
        $ids = [];

        if ($user->hasRole('Administrator')) {
            $libraries = Library::all();

            foreach ($libraries as $library) {
                array_push($ids, $library->getId());
            }
        } else {
            $privileges = LibraryPrivilege::where('user_id', '=', $user->getId())->get();

            foreach ($privileges as $privilege) {
                array_push($ids, $privilege->getLibraryId());
            }
        }

        return $ids;
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
